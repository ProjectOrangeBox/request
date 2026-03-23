<?php

declare(strict_types=1);

namespace orange\request;

use orange\request\RequestAttribute;
use ReflectionClass;

/**
 * Request class for handling form input validation and data management.
 *
 * This class uses PHP reflection to discover class properties with RequestAttribute
 * annotations and automatically validates and filters input data based on those rules.
 * It organizes validated data into multiple formats for flexible access patterns.
 *
 * SOLID Principles Applied:
 * - Single Responsibility: Handles only input validation and data organization
 * - Open/Closed: Extensible through RequestAttribute annotations without modifying core logic
 * - Interface Segregation: Provides multiple access methods (asArray, asTable, asColumns) for client flexibility
 * - Dependency Inversion: Depends on RequestAttribute abstraction rather than concrete validators
 *
 * @property array $errors Validation errors grouped by field name
 * @property array $fieldSet Mapping of property names to their validation attributes
 * @property array $db Validated data organized by table and column structure
 * @property array $array Validated data in simple associative array format
 */
class Request
{
    protected array $errors = [];
    protected array $fieldSet = [];
    protected array $db = [];
    protected array $array = [];

    /**
     * Initializes a Request instance with input data and processes field attributes.
     *
     * Uses reflection to discover properties with RequestAttribute annotations,
     * then processes each property through its validation rules.
     *
     * @param array $input The input data to be validated and processed
     */
    public function __construct(protected array $input)
    {
        // use reflection to get the properties and their attributes
        $reflectionClass = new ReflectionClass(get_class($this));

        // loop through the properties and get their attributes
        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = [];

            foreach ($property->getAttributes() as $attribute) {
                $attributeClassName = $attribute->getName();

                if ((new ReflectionClass($attributeClassName))->isSubclassOf(RequestAttribute::class)) {
                    $attributes[$this->getClass($attributeClassName)] = $attribute->newInstance();
                }
            }

            if (!empty($attributes)) {
                $this->fieldSet[$property->getName()] = $attributes;
            }
        }

        // now we can loop through the field set and process the attributes for each property
        foreach ($this->fieldSet as $property => $attributes) {
            $this->process($property, $attributes, $this->fieldSet);
        }
    }

    /**
     * Determines if the request passed all validation rules.
     *
     * @return bool True if there are no validation errors, false otherwise
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Returns all validation errors grouped by field name.
     *
     * @return array An associative array of field names to arrays of error messages
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Returns validated data organized by database table structure.
     *
     * @param false|string $tablename Optional table name to retrieve specific table data; returns all tables if false
     * @return array The table or column data structure
     * @throws \Exception When the requested table name is not found
     */
    public function asTable(false|string $tablename = false): array
    {
        $db = $this->db['tables'];

        if ($tablename) {
            if (!isset($this->db['tables'][$tablename])) {
                throw new \Exception('Table ' . $tablename . ' not found.');
            }

            $db = $this->db['tables'][$tablename];
        }

        return $db;
    }

    /**
     * Returns validated data organized by column name.
     *
     * @return array An associative array of column names to their validated values
     */
    public function asColumns(): array
    {
        return $this->db['columns'];
    }

    /**
     * Returns validated data as a simple associative array.
     *
     * @return array An associative array of property names to their validated values
     */
    public function asArray(): array
    {
        return $this->array;
    }

    /**
     * Extracts the short class name from a fully qualified class name.
     *
     * @param string $className The fully qualified class name
     * @return string The short class name without namespace
     */
    protected function getClass($className): string
    {
        // Find the position of the last backslash
        $lastSlashPos = strrpos($className, '\\');

        if ($lastSlashPos === false) {
            // No namespace separator found, so the whole string is the class name
            $shortName = $className;
        } else {
            // Extract the substring after the last backslash
            $shortName = substr($className, $lastSlashPos + 1);
        }
        return $shortName;;
    }

    /**
     * Processes a property by applying validation rules and filters from its attributes.
     *
     * Validates the input value against all rules, applies filters, and stores valid
     * data in the database and array structures.
     *
     * @param string $property The property name to process
     * @param array $attributes The validation attributes for the property
     * @return void
     */
    protected function process(string $property, array $attributes): void
    {
        // get the field name, column name, table name and human name from the attributes

        // form field name
        $fieldName = $this->findBy($property, 'FieldName', $attributes);

        // table column name
        $column = $this->findBy($property, 'Column', $attributes);

        // table name
        $table = $this->findBy($property, 'Table', $attributes);

        // human readable name for error messages
        $label = $this->findBy($property, 'Label', $attributes);

        // get the value from the input
        $value = $this->input[$fieldName] ?? '';

        // assume the value is valid until a validation rule fails
        $isValid = true;

        // loop through the attributes and apply validation and filtering
        foreach ($attributes as $rule) {
            // send a copy of this request into the rule so it can access other fields if needed
            $rule->request($this);

            // do validation
            if (method_exists($rule, 'validate')) {
                if (!$rule->validate($value)) {
                    $this->errors[$fieldName][] = $rule->getMessage($label);
                    $isValid = false;
                }
            }
            // do filter
            if (method_exists($rule, 'filter')) {
                $value = $rule->filter($value);
            }
        }

        // if the value is valid assign it to the class and add it to the db array properties
        if ($isValid) {
            // assign the value to the class and add it to the db array properties
            $this->whenValid($property, $value, $table, $column);
        }
    }

    /**
     * Stores validated data across multiple storage formats.
     *
     * Assigns the validated value to the class property, and stores it in the
     * array and database table/column structures for flexible data access.
     *
     * @param string $property The property name to assign the value to
     * @param mixed $value The validated value to store
     * @param string $table The database table name
     * @param string $column The database column name
     * @return void
     */
    protected function whenValid($property, $value, $table, $column): void
    {
        // assign to the class
        $this->$property = $value;

        // assign to the array for easy access
        $this->array[$property] = $value;

        // if valid add it to the db array
        $this->db['tables'][$table][$column] = $value;
        $this->db['columns'][$column] = $value;
    }

    /**
     * Finds an attribute value by key name using case-insensitive matching.
     *
     * @param string $property The property name (used as default if key not found)
     * @param string $key The attribute key to search for
     * @param array $attributes The attributes to search through
     * @return string The value from the matching attribute or the property name if not found
     */
    protected function findBy(string $property, string $key, array $attributes): string
    {
        $fieldName = $property;

        foreach ($attributes as $attrName => $attribute) {
            if (strtolower($attrName) == strtolower($key)) {
                $fieldName = $attribute->getName();

                break;
            }
        }

        return $fieldName;
    }
}

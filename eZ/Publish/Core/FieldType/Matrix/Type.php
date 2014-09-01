<?php
/**
 * Matrix FieldType
 * User: joe
 * Date: 12/12/13
 * Time: 8:59 PM
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Matrix;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;

/**
 * Matrix Field Type
 *
 * This type implements the ezmatrix field type.
 *
 * Valid hash format:
 *
 * <code>
 * $hash = array(
 *       'columns' => array(
 *           array(
 *               'id' => 'make',
 *               'name' => 'Make',
 *               'num' => 0
 *           ),
 *           array(
 *               'id' => 'model',
 *               'name' => 'Model',
 *               'num' => 1
 *           ),
 *           array(
 *               'id' => 'year',
 *               'name' => 'Year',
 *               'num' => 2
 *           )
 *       ),
 *       'rows' => array(
 *           array(
 *               'make' => 'Porsche',
 *               'model' => '911',
 *               'year' => '2001'
 *           ),
 *           array(
 *               'make' => 'Lamborghini',
 *               'model' => 'Diablo',
 *               'year' => '2005'
 *           )
 *       )
 *    );
 * </code>
 *
 * @package Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Matrix
 */

class Type extends FieldType
{

    const COLUMN_ID_KEY = 'id',
        COLUMN_LABEL_KEY = 'label';

    protected $validatorConfigurationSchema = array(
        'MatrixValidator' => array(
            'columns' => array(
                'type' => 'bool',
                'default' => false
            )
        )
    );

    /**
     * @var array
     */
    protected $settingsSchema = array(
        'columns' => array(
            'type' => 'array',
            'default' => array(),
        ),
        'defaultNRows' => array(
            'type' => 'int',
            'default' => 1
        )
    );

    public function getFieldTypeIdentifier()
    {
        return "ezmatrix";
    }

    protected function createValueFromInput( $inputValue )
    {
        if ( is_array( $inputValue ) )
        {
            $inputValue = new Value( $inputValue );
        }
        return $inputValue;
    }

    protected function checkValueStructure( BaseValue $value )
    {
        if ( ! $value->rows instanceof RowCollection )
        {
            throw  new InvalidArgumentType(
                '$value->rows',
                'RowCollection',
                $value->rows
            );
        }
    }

    public function getName( SPIValue $value )
    {
        return $value->name;
    }

    public function getEmptyValue()
    {
        return new Value();
    }

    public function fromHash( $hash )
    {
        $rows = array();
        $columns = array();

        if ( isset( $hash['rows'] ) )
        {
            $rows = array_map(
                function ( $row )
                {
                    return new Row( $row );
                },
                $hash['rows']
            );
        }

        if ( isset( $hash['columns'] ) )
        {
            $columns = array_map(
                function ( $column )
                {
                    return new Column( $column );
                },
                $hash['columns']
            );
        }

        return new Value( $rows, $columns );
    }

    public function toHash( SPIValue $value )
    {
        return array(
            'rows' => !empty( $value->rows ) ? $value->rows->toArray() : array(),
            'columns' => !empty( $value->columns ) ? $value->columns->toArray() : array()
        );
    }

    /**
     * Validates the fieldSettings of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct
     *
     * Settings can be provided in this form
     *
     * $fieldSettings = array(
     *     'columns' => array(
     *         array(
     *            'id' => 'col_id'
     *            'label' => 'COL LABEL',
     *         ),
     *         array(
     *            'id' => 'col_id_2'
     *            'label' => 'COL LABEL_2',
     *         )
     *     ),
     *     'defaultNRows' => 5
     * );
     *
     * Cols will be added in the provided order
     *
     * @param mixed $fieldSettings
     *
     * @return array|\eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateFieldSettings( $fieldSettings )
    {
        $validationErrors = array();

        if ( !isset( $fieldSettings['columns'] ) )
        {
            $validationErrors[] = new ValidationError(
                "You need to provide columns in order create this field",
                null
            );
        }

        foreach ( $fieldSettings as $name => $value )
        {
            if ( isset( $this->settingsSchema[$name] ) )
            {
                switch ( $name )
                {
                    case "columns":
                        if ( !is_array( $value ) )
                        {
                            $validationErrors[] = new ValidationError(
                                "Setting '%setting%' must be an array",
                                null,
                                array(
                                    "setting" => $name
                                )
                            );
                        }
                        else if ( empty( $value ) )
                        {
                            $validationErrors[] = new ValidationError(
                                "You need to provide at least one column",
                                null,
                                array(
                                    "setting" => $name
                                )
                            );
                        }
                        else
                        {
                            foreach ( $value as $column )
                            {
                                $keys = array_keys( $column );
                                $values = array_values( $column );
                                $diff = array_diff( $keys, array(
                                        self::COLUMN_ID_KEY, self::COLUMN_LABEL_KEY
                                    ));
                                if ( !empty( $diff) )
                                {
                                    $validationErrors[] = new ValidationError(
                                        "Columns must be provided as an associative array with the following keys:
                                       " . self::COLUMN_ID_KEY . ", " . self::COLUMN_LABEL_KEY,
                                        'null',
                                        array(
                                            "setting" => $name
                                        )
                                    );
                                }

                                foreach ( $values as $value )
                                {
                                    if ( !is_string( $value ) )
                                    {
                                        $validationErrors[] = new ValidationError(
                                            "Values for columns id and columns label can only be string",
                                            'null',
                                            array(
                                                "setting" => $name
                                            )
                                        );
                                        break;
                                    }
                                }
                            }
                        }
                        break;
                    case 'defaultNRows':
                        if ( ( !is_integer( $value ) ) || ( $value < 1 ) )
                        {
                            $validationErrors[] = new ValidationError(
                                "Number of Rows must be an integer greater than 0",
                                'null',
                                array(
                                    "setting" => 'defaultNRows'
                                )
                            );
                        }
                        break;
                }
            }
            else
            {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    array(
                        "setting" => $name
                    )
                );
            }
        }

        return $validationErrors;
    }

    protected function getSortInfo( BaseValue $value )
    {
        return (string) $value;
    }

    public function validate( FieldDefinition $fieldDefinition, SPIValue $fieldValue )
    {
        $validationErrors = array();

        if ( $this->isEmptyValue( $fieldValue ) )
        {
            return $validationErrors;
        }

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        foreach( $validatorConfiguration as $name => $validator )
        {
            switch ( $name )
            {
                case 'MatrixValidator':
                    $columns = $validator['columns'];

                    if ( array_intersect( $fieldValue->rows->columnIds, $columns ) != $columns )
                    {
                        $validationErrors[] = new ValidationError(
                            "Keys of provided rows are not equal to the column names",
                            "",
                            array(
                                "columns" => $columns
                            )
                        );
                    }
            }
        }

        return $validationErrors;
    }

    public function validateValidatorConfiguration( $validatorConfiguration )
    {
        $validationErrors = array();

        foreach ( $validatorConfiguration as $validatorIdentifier => $constraints )
        {
            if ( $validatorIdentifier !== 'MatrixValidator' )
            {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    array(
                        "validator" => $validatorIdentifier
                    )
                );

                continue;
            }
        }

        return $validationErrors;
    }
}

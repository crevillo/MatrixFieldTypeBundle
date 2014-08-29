<?php
/**
 * This file is part of the EzMatrixBundle package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Blend\EzMatrixBundle\Tests\eZ\Publish\Core\FieldType\Matrix;

use eZ\Publish\Core\FieldType\Tests\FieldTypeTest;
use Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Matrix\Type as MatrixType;
use Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Matrix\Value as MatrixValue;
use Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Matrix\Row;
use Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Matrix\Column;
use eZ\Publish\Core\FieldType\ValidationError;

/**
 * @group fieldType
 * @group ezmatrix
 * @covers \Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Matrix\Type
 * @covers \Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Matrix\Value
 */
class MatrixTest extends FieldTypeTest
{
    protected function createFieldTypeUnderTest()
    {
        $fieldType = new MatrixType();
        $fieldType->setTransformationProcessor( $this->getTransformationProcessorMock() );

        return $fieldType;
    }

    protected function getValidatorConfigurationSchemaExpectation()
    {
        return array(
            'MatrixValidator' => array(
                'columns' => array(
                    'type' => 'bool',
                    'default' => false
                )
            )
        );
    }

    protected function getSettingsSchemaExpectation()
    {
        return array(
            'columns' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'defaultNRows' => array(
                'type' => 'int',
                'default' =>1,
            )
        );
    }

    protected function getEmptyValueExpectation()
    {
        return new MatrixValue;
    }

    protected function getColumnConfig()
    {
        return array(
            new Column(
                array(
                    'name' => 'name',
                    'id' => 'name',
                    'num' => 0
                )
            ),
            new Column(
                array(
                    'name' => 'quest',
                    'id' => 'quest',
                    'num' => 1
                )
            ),
            new Column(
                array(
                    'name' => 'colour',
                    'id' => 'colour',
                    'num' => 2
                )
            )
        );
    }

    protected function getColumnConfigHash()
    {
        return array(
            array(
                'id' => 'name',
                'name' => 'name',
                'num' => 0
            ),
            array(
                'id' => 'quest',
                'name' => 'quest',
                'num' => 1
            ),
            array(
                'id' => 'colour',
                'name' => 'colour',
                'num' => 2
            )
        );
    }

    protected function getSingleRow()
    {
        return array(
            new Row(
                array(
                    'name' => 'Lancelot',
                    'quest' => 'Grail',
                    'colour' => 'blue'
                )
            )
        );
    }

    protected function getSingleRowHash()
    {
        return array(
            array(
                'name' => 'Lancelot',
                'quest' => 'Grail',
                'colour' => 'blue'
            )
        );
    }

    protected function getMultipleRows()
    {
        $rows = $this->getSingleRow();
        $rows[] = new Row(
            array(
                'name' => 'Gallahad',
                'quest' => 'Seek Grail',
                'colour' => 'Blue! no, Red! Augh!'
            )
        );

        return $rows;
    }

    public function provideInvalidInputForAcceptValue()
    {
        return array(
            array(
                'My name',
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentType',
            ),
            array(
                23,
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentType',
            )
            /*array(
                array( 'foo' ),
                'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentType',
            )*/
        );
    }


    public function provideValidInputForAcceptValue()
    {
        return array(
            array(
                array(),
                new MatrixValue()
            ),
            array(
                $this->getSingleRow(),
                new MatrixValue(
                    $this->getSingleRow(),
                    $this->getColumnConfig()
                )
            ),
            array(
                $this->getMultipleRows(),
                new MatrixValue(
                    $this->getMultipleRows(),
                    $this->getColumnConfig()
                )
            )
        );
    }

    public function provideInputForToHash()
    {
        return array(
            array(
                new MatrixValue(
                    array(
                        'rows' => $this->getSingleRow(),
                        'columns' => $this->getColumnConfig()
                    )
                ),
                array(
                    'rows' => $this->getSingleRowHash(),
                    'columns' => $this->getColumnConfigHash()
                ),
            )
        );
    }

    public function provideInputForFromHash()
    {
        return array(
            array(
                array(
                    'rows' => $this->getSingleRowHash(),
                    'columns' => $this->getColumnConfigHash()
                ),
                new MatrixValue(
                    $this->getSingleRow(),
                    $this->getColumnConfig()
                )
            )
        );
    }

    protected function provideFieldTypeIdentifier()
    {
        return 'ezmatrix';
    }

    public function provideDataForGetName()
    {
        return array(
            array(
                new MatrixValue(
                    array(
                        'rows' => $this->getSingleRow(),
                        'colummns' => $this->getColumnConfig()
                    )
                ),
                'name'
            ),
            array(
                new MatrixValue(
                    array(
                        'rows' => $this->getMultipleRows(),
                        'colummns' => $this->getColumnConfig()
                    )
                ),
                'name'
            ),
            array(
                new MatrixValue(
                    array(
                        'rows' => $this->getSingleRow()
                    )
                ),
                'name'
            ),
            array(
                new MatrixValue(
                    array(
                        'rows' => $this->getMultipleRows()
                    )
                ),
                'name'
            ),
            array(
                new MatrixValue(),
                ''
            )
        );
    }

    public function provideValidDataForValidate()
    {
        return array(
            array(
                array(),
                new MatrixValue(
                    $this->getSingleRow()
                )
            ),
            array(
                array(),
                new MatrixValue(
                    $this->getMultipleRows()
                )
            ),
            array(
                array(),
                new MatrixValue(
                    $this->getMultipleRows(),
                    $this->getColumnConfig()
                )
            )
        );
    }

    public function provideInvalidDataForValidate()
    {
        return array(
            array(
                array(
                    "MatrixValidator" => array(
                        'columns' => array( 'id', 'label' ),
                    ),
                ),
                new MatrixValue(
                    array(
                        "rows" => array(
                            array(
                                'id' => 'dummy',
                                'label3' => 'text'
                            )
                        )
                    )
                ),
                array(
                    new ValidationError(
                        "Keys of provided rows are not equal to the column names",
                        "",
                        array(
                            "columns" => array( 'id', 'label' )
                        )
                    )
                ),
            )
        );
    }

    public function provideValidFieldSettings()
    {
        return array(
            array(
                array(
                    'columns' => array(
                        array(
                            'id' => 'col_id',
                            'label' => 'COL LABEL',
                        )
                    )
                )
            ),
            array(
                array(
                    'columns' => array(
                        array(
                            'id' => 'col_id',
                            'label' => 'COL LABEL',
                        ),
                        array(
                            'id' => 'col_id_2',
                            'label' => 'COL LABEL 2',
                        )
                    )
                )
            ),
            array(
                array(
                    'columns' => array(
                        array(
                            'id' => 'col_id',
                            'label' => 'COL LABEL',
                        ),
                        array(
                            'id' => 'col_id_2',
                            'label' => 'COL LABEL 2',
                        )
                    ),
                    'defaultNRows' => 10
                )
            )
        );
    }

    public function provideInValidFieldSettings()
    {
        return array(
            array(
                array()
            ),
            array(
                array(
                    'columns' => 23,
                )
            ),
            array(
                array(
                    'columns' => array(),
                )
            ),
            array(
                array(
                    'columns' => array(
                        array(
                            'foo' => 'bar',
                            'foo2' => 'bar2'
                        )
                    ),
                )
            ),
            array(
                array(
                    'columns' => array(
                        array(
                            'id' => 'bar',
                            'foo_label' => 'bar2'
                        )
                    ),
                )
            ),
            array(
                array(
                    'columns' => array(
                        array(
                            'id' => 'bar',
                            'label' => 'bar2'
                        ),
                        array(
                            'id2' => 'bar',
                            'label2' => 'bar2'
                        )
                    ),
                )
            ),
            array(
                array(
                    'columns' => array(
                        array(
                            'id' => 'bar',
                            'label' => 'bar2'
                        ),
                        array(
                            'id' => 'bar',
                            'label' => 'bar2'
                        )
                    ),
                    'defaultNRows' => 0
                )
            )
        );
    }
}

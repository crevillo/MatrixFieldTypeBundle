<?php
/**
 * Created by PhpStorm.
 * User: carlos
 * Date: 29/08/14
 * Time: 13:20
 */

namespace Blend\EzMatrixBundle\eZ\Publish\Core\FieldType\Validator;

class MatrixValidator extends Validator
{
    protected $constraints = array(
        "columns" => false,
    );

    protected $constraintsSchema = array(
        "columns" => array(
            "type" => "array",
            "default" => array()
        )
    );
}


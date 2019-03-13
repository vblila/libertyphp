<?php

namespace Libertyphp\Database;

abstract class ModelTableMapper
{
    abstract static function getAttributesMap(): array;

    /**
     * @param array $data
     * @param mixed $model
     * @return mixed
     */
    public static function fillModelFromRow(array $data, $model)
    {
        foreach (static::getAttributesMap() as $modelAttribute => $tableAttribute) {
            $model->{$modelAttribute} = $data[$tableAttribute];
        }
        return $model;
    }

    /**
     * @param mixed $model
     * @return array
     */
    public static function fillRowFromModel($model): array
    {
        $row = [];
        foreach (static::getAttributesMap() as $modelAttribute => $tableAttribute) {
            $row[$tableAttribute] = $model->{$modelAttribute};
        }
        return $row;
    }
}

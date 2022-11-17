<?php

namespace StarLine\Entity;

use Exception;
use ReflectionObject;
use StarLine\Attribute\Field;

class Entity
{
    protected array $extra = [];

    protected function __construct() {}

    function __get(string $name)
    {
        return $this->extra[$name] ?? null;
    }

    /**
     * @param array $params массив значений
     * @return Entity
     * @throws Exception
     */
    public static function make(array $params): self
    {
        $entity = new static();

        $ro = new ReflectionObject($entity);
        foreach ($ro->getProperties() as $rp) {
            $ras = $rp->getAttributes(Field::class);
            if (count($ras) > 0) {
                $ra = $ras[0]->newInstance();
                $type = $ra->elementsType;
                if (isset($params[$ra->name])) {
                    // значение свойства нам передано
                    if ($type !== null) {
                        //todo: свойство является массивом других сущностей
                        $values = array_map(function ($p) use ($type) {
                            return $type::make($p);
                        }, $params[$ra->name]);
                        $rp->setValue($entity, $values);
                    } else {
                        $rp->setValue($entity, $params[$ra->name]);
                    }
                } elseif ($rp->hasDefaultValue()) {
                    //todo: есть значение по умолчанию
                } else {
                    throw new Exception('not passed a mandatory property: ' . $rp->getName());
                }
            }
        }

        return $entity;
    }

    public static function fill(array $data): self
    {
        $map = [];

        $entity = new static();

        $ro = new ReflectionObject($entity);
        foreach ($ro->getProperties() as $rp) {
            $key = $rp->getName();
            $type = $rp->getType()->getName();
            $array_of = null;
            $name = $rp->getName();

            $ras = $rp->getAttributes(Field::class);
            if (count($ras) > 0) {
                /**
                 * @var Field $ra
                 */
                $ra = $ras[0]->newInstance();
                $key = $ra->from_name ?? $key;
                $array_of = $ra->array_of ?? null;
            }

            $map[$key] = [
                'name' => $name,
                'type' => $type,
                'array_of' => $array_of
            ];
        }
        unset($key, $type, $array_of, $name);

        foreach ($data as $key => $value) {
            if (isset($map[$key])) {
                // приведение значения к нужному типу.
                // если это объект/массив объектов, то создаём объекты нужного типа
                $entity->$map[$key]['name'] = $value;
            } else {
                $entity->extra[$map[$key]['name']] = $value;
            }
        }

        return $entity;
    }
}
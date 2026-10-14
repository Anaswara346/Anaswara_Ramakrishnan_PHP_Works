<?php

class ParentClass{
    public static function show()
    {
        echo static::class;
    }
}

class ChildClass extends ParentClass
{

}
ChildClass::show();
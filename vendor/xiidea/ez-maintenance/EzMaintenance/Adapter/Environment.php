<?php

/*
 * This file is part of the EzMaintenance package.
 *
 * (c) Xiidea <http://www.xiidea.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
namespace EzMaintenance\Adapter;

class Environment extends  BaseVariableAdapter
{
    public static function checkVars($var, $value)
    {
        return getenv($var) == $value;
    }
}
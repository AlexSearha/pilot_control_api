<?php

namespace App\Enum;

enum CompanyClientOverloadTypeEnum : string
{
    case SELECT     = 'SELECT';
    case STRING     = 'STRING';
    case NUMBER     = 'NUMBER';
    case BOOLEAN    = 'BOOLEAN';
    case DATE       = 'DATE';
    case DATETIME   = 'DATETIME';
    case EMAIL      = 'EMAIL';
    case TEXT       = 'TEXT';
}

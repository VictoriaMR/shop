<?php 

namespace app\service\attr;

class Attr
{
    public function name()
    {
        return make('app/service/attr/Name');
    }

    public function nameLanguage()
    {
        return make('app/service/attr/NameLanguage');
    }

    public function nameMap()
    {
        return make('app/service/attr/NameMap');
    }

    public function value()
    {
        return make('app/service/attr/Value');
    }

    public function valueLanguage()
    {
        return make('app/service/attr/ValueLanguage');
    }

    public function valueMap()
    {
        return make('app/service/attr/ValueMap');
    }
}
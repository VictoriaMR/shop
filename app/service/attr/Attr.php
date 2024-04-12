<?php 

namespace app\service\attr;

class Attr
{
    public function name()
    {
        return service('attr/Name');
    }

    public function nameLanguage()
    {
        return service('attr/NameLanguage');
    }

    public function nameMap()
    {
        return service('attr/NameMap');
    }

    public function value()
    {
        return service('attr/Value');
    }

    public function valueLanguage()
    {
        return service('attr/ValueLanguage');
    }

    public function valueMap()
    {
        return service('attr/ValueMap');
    }
}
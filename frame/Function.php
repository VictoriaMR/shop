<?php
function purchase() {
    return make('app/service/purchase/Purchase');
}
function attr() {
    return make('app/service/attr/Attr');
}
function site() {
    return make('app/service/site/Site');
}
function category() {
    return make('app/service/category/Category');
}
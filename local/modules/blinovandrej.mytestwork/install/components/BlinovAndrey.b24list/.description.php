<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
        "NAME" => GetMessage("SECTION_NAME"),
        "DESCRIPTION" => GetMessage("DESC"),
        "ICON" => "/images/icon.gif",
        "CACHE_PATH" => "Y",
        "PATH" => array(
                "ID" => "TESTWORK",
                "NAME" => GetMessage("SECTION_NAME"),
                "CHILD" => array(
                        "ID" => "TESTWORK_BLINOV",
                        "NAME" => GetMessage("CHILD_NAME")
                )
        ),
);

?>
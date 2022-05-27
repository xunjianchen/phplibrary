<?php

namespace Phplibrary;

class A2xml
{
    private static $version = '1.0';
    private static $encoding = 'GBK';
    private static $root = 'MSG';
    private static $xml = null;

    // 使用XmlWriter生成xml  当数组下有数字索引时生成有问题
    // 例如数组['item'=>[['a'=>1,'b'=>2],['a'=>2,'b'=>4]]]
    public static function toXml($data, $eIsArray = FALSE)
    {
        if (is_null(self::$xml)) {
            self::$xml = new \XmlWriter();
        }
        if (!$eIsArray) {
            self::$xml->openMemory();
            self::$xml->startDocument(self::$version, self::$encoding);
            self::$xml->startElement(self::$root);
            self::$xml->writeAttribute('version', '1.5');
        }
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                self::$xml->startElement($key);
                self::toXml($value, TRUE);
                self::$xml->endElement();
                continue;
            }
            self::$xml->writeElement($key, $value);
        }
        if (!$eIsArray) {
            self::$xml->endElement();
            return mb_convert_encoding(self::$xml->outputMemory(true), "utf-8", "gbk");
        }
    }

    //另一种方式生成xml
    public function xmlEncode($data, $encoding = 'GBK')
    {
        $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $xml .= '<MSG version="1.5">';
        $xml .= self::dataToXml($data);
        $xml .= "</MSG>";
        return $xml;
    }

    /**
     * 数据XML编码
     * @param array $data 数据
     * @param string $item  数字索引的子节点名   <item id="0">
     * @param string $id  数字索引key转换为的属性名  <item id="1">
     * @return string
     */
    public static function dataToXml(array $data, string $item = 'item',string $id='id') :string
    {
        $xml = $attr =  '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $attr = " $id=\"{$key}\"";
                $key = $item;
            }
            $xml .= "<$key$attr>";
            $xml .= (is_array($val) || is_object($val)) ? self::dataToXml($val, $key) : $val;
            $xml .= "</$key>";
        }
        return $xml;
    }

    public static function xmlToArray($xml)
    {
        //$xml = mb_convert_encoding(base64_decode($xml), "gbk", "utf-8");
        //将XML转为array
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

}
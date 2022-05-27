<?php
/**
 * Created by PhpStorm.
 * User: cjx
 * Date: 2022/5/27
 * Time: 9:03
 */

namespace Phplibrary;

class Xml
{

    // 输出参数
    protected static $options = [
        // 根节点名
        'root_node' => 'root',
        // 根节点属性
        'root_attr' => '',
        //数字索引的子节点名
        'item_node' => 'item',
        // 数字索引子节点key转换的属性名
        'item_key' => 'id',
        // 数据编码
        'encoding' => 'utf-8',
    ];

    /**
     * 处理数据
     * @access protected
     * @param mixed $data 要处理的数据
     * @return mixed
     */
    public static function arrayToXml(array $data): string
    {
        if (is_string($data)) {
            if (0 !== strpos($data, '<?xml')) {
                $encoding = self::$options['encoding'];
                $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
                $data = $xml . $data;
            }
            return $data;
        }
        // XML数据转换
        return self::xmlEncode($data, self::$options['root_node'], self::$options['item_node'], self::$options['root_attr'], self::$options['item_key'], self::$options['encoding']);
    }

    /**
     * XML编码
     * @access protected
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param mixed $attr 根节点属性
     * @param string $id 数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    protected static function xmlEncode($data, string $root, string $item, $attr, string $id, string $encoding): string
    {
        if (is_array($attr)) {
            $array = [];
            foreach ($attr as $key => $value) {
                $array[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $array);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml .= "<{$root}{$attr}>";
        $xml .= self::dataToXml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }

    /**
     * 数据XML编码
     * @access protected
     * @param mixed $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id 数字索引key转换为的属性名
     * @return string
     */
    protected static function dataToXml(array $data, string $item, string $id): string
    {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (is_array($val) || is_object($val)) ? self::dataToXml($val, $item, $id) : $val;
            $xml .= "</{$key}>";
        }
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml xml字符串或者xml文件名
     * @param bool $isfile 传入的是否是xml文件名
     * @return array  转换得到的数组
     */
    public static function xmlToArray(string $xml, bool $isfile = false): array
    {
        if ($isfile) {
            if (!file_exists($xml)) {
                return [];
            }
            $xmlstr = file_get_contents($xml);
        } else {
            $xmlstr = $xml;
        }
        return json_decode(json_encode(simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}
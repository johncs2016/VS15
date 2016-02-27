<?php
    include "data.php";
    include "XML.php";

    // function defination to convert array to xml
    function generate_xml_element( $dom, $data ) {
        if ( empty( $data['name'] ) )
            return false;

        // Create the element
        $element_value = ( ! empty( $data['value'] ) ) ? $data['value'] : null;
        $element = $dom->createElement( $data['name'], $element_value );

        // Add any attributes
        if ( ! empty( $data['attributes'] ) && is_array( $data['attributes'] ) ) {
            foreach ( $data['attributes'] as $attribute_key => $attribute_value ) {
                $element->setAttribute( $attribute_key, $attribute_value );
            }
        }

        // Any other items in the data array should be child elements
        foreach ( $data as $data_key => $child_data ) {
            if ( ! is_numeric( $data_key ) )
                continue;

            $child = generate_xml_element( $dom, $child_data );
            if ( $child )
                $element->appendChild( $child );
        }

        return $element;
    }

    // Create Test Object
    $obj = new weeklydata();
    $obj->setID(1);
    $obj->setUserID(1);
    $obj->setObsDate(new DateTime());
    $obj->setWeight(87.5);
    $obj->setWaistSize(90.3);

    // initializing or creating array
    $data = (array)$obj;
    $keys = array_keys($data);

    // fix all private and protected property keys
    foreach ($keys as $key) {
        $position = strpos($key, '_');
        if ($position) {
            $start = strlen($key) - $position;
            $data[substr($key, strlen($key) - $start + 1)] = $data[$key];
            unset($data[$key]);
        }
    }

    // convert any objects to arrays
    $root = array('name' => 'weeklydata');
    foreach ($data as $key => $value) {
        try {
            if (is_object($value)) {
                $val = (get_class($value) == 'DateTime' ? $value->format('Y-m-d H:i:s') : '(object)');
            } else {
                $val = $value;
            }
            $root[] = array('name' => $key, 'value' => $val);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    $doc = new DOMDocument();
    $child = generate_xml_element( $doc, $root );
    if ( $child ) {
        $doc->appendChild( $child );
    }
    $doc->formatOutput = true; // Add whitespace to make easier to read XML
    $xml = $doc->saveXML();

    echo $xml;

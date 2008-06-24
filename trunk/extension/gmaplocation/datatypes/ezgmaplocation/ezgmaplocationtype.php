<?php
//
// Definition of eZGmapLocationType class
//
// SOFTWARE NAME: Blend Gmap Location Class
// SOFTWARE RELEASE: 0.3
// COPYRIGHT NOTICE: Copyright (C) 2006 Blend Interactive
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
// 
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
// 
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//


/*! \file ezgmaplocationtype.php
*/

/*!
  \class eZGmapLocationType ezgmaplocationtype.php
  \ingroup eZDatatype
  \brief The class eZGmapLocationType does

*/
include_once( "kernel/classes/ezdatatype.php" );
include_once( "extension/gmaplocation/datatypes/ezgmaplocation/ezgmaplocation.php" );

define( "EZ_GMAPLOCATION_DEFAULT_NAME_VARIABLE", "_ezgmaplocation_default_name_" );


define( "EZ_DATATYPESTRING_GMAPLOCATION", "ezgmaplocation" );

class eZGmapLocationType extends eZDataType
{
    /*!
     Constructor
    */
    function eZGmapLocationType()
    {
        $this->eZDataType( EZ_DATATYPESTRING_GMAPLOCATION, ezi18n( 'extension/gmaplocation/datatypes', "GMaps Location", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    function validateObjectAttributeHTTPInput( &$http, $base, &$contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_data_gmaplocation_latitude_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_data_gmaplocation_longitude_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {

            $latitude = $http->postVariable( $base . '_data_gmaplocation_latitude_' . $contentObjectAttribute->attribute( 'id' ) );
            $longitude = $http->postVariable( $base . '_data_gmaplocation_longitude_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute =& $contentObjectAttribute->contentClassAttribute();
            if ( $latitude == '' or
                 $longitude == '' )
            {
                if ( ( !$classAttribute->attribute( 'is_information_collector' ) and
                       $contentObjectAttribute->validateIsRequired() ) )
                {
                //TODO: In this case, we should directly call for geocoding.
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                     'Missing Latitude/Longitude input.' ) );
                    return EZ_INPUT_VALIDATOR_STATE_INVALID;
                }
                else
                    return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
            }
        }
        else
        {
            return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
        }


    }

    function fetchObjectAttributeHTTPInput( &$http, $base, &$contentObjectAttribute )
    {

        $latitude = $http->postVariable( $base . '_data_gmaplocation_latitude_' . $contentObjectAttribute->attribute( 'id' ) );
        $longitude = $http->postVariable( $base . '_data_gmaplocation_longitude_' . $contentObjectAttribute->attribute( 'id' ) );


        $location = new eZGmapLocation( $latitude, $longitude );

        $contentObjectAttribute->setContent( $location );
        return true;
    }

    function storeObjectAttribute( &$contentObjectAttribute )
    {
        $location =& $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( "data_text", $location->xmlString() );
    }

    function &objectAttributeContent( &$contentObjectAttribute )
    {
        $location = new eZGmapLocation( '', '', '' );
        $location->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $location;
    }

    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    function title( &$contentObjectAttribute )
    {
        $location = new eZGmapLocation( '', '', '' );
        $location->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $location->attribute('latitude') . ', ' . $location->attribute('longitude');
    }

    function hasObjectAttributeContent( &$contentObjectAttribute )
    {
        return true;
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( &$contentObjectAttribute, $currentVersion, &$originalContentObjectAttribute )
    {
        if ( $currentVersion == false )
        {
            $location =& $contentObjectAttribute->content();
            $contentClassAttribute =& $contentObjectAttribute->contentClassAttribute();
            if ( !$location )
            {
                $location = new eZGmapLocation( $contentClassAttribute->attribute( 'data_text1' ), '', '' );
            }
            else
            {
                $location->setLatitude('');
                $location->setLongitude('');
            }
            $contentObjectAttribute->setAttribute( "data_text", $location->xmlString() );
            $contentObjectAttribute->setContent( $location );
        }
    }

    /*!
     \reimp
    */
    function fetchClassAttributeHTTPInput( &$http, $base, &$classAttribute )
    {
        $defaultValueName = $base . EZ_GMAPLOCATION_DEFAULT_NAME_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $defaultValueName ) )
        {
            $defaultValueValue = $http->postVariable( $defaultValueName );

            if ($defaultValueValue == ""){
                $defaultValueValue = "";
            }
            $classAttribute->setAttribute( 'data_text1', $defaultValueValue );
            return true;
        }
        return false;
    }

    /*!
     \reimp
    */
    function serializeContentClassAttribute( &$classAttribute, &$attributeNode, &$attributeParametersNode )
    {
        $defaultName = $classAttribute->attribute( 'data_text1' );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'default-name', $defaultName ) );
    }

    /*!
     \reimp
    */
    function unserializeContentClassAttribute( &$classAttribute, &$attributeNode, &$attributeParametersNode )
    {
        $defaultName = $attributeParametersNode->elementTextContentByName( 'default-name' );
        $classAttribute->setAttribute( 'data_text1', $defaultName );
    }

    /*!
     \reimp
    */
    function serializeContentObjectAttribute( &$package, &$objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $xml = new eZXML();
        $domDocument = $xml->domTree( $objectAttribute->attribute( 'data_text' ) );
        $node->appendChild( $domDocument->root() );

        return $node;
    }

    /*!
     \reimp
    */
    function unserializeContentObjectAttribute( &$package, &$objectAttribute, $attributeNode )
    {
        $rootNode = $attributeNode->firstChild();
        $xmlString = $rootNode->attributeValue( 'local_name' ) == 'data-text' ? $rootNode->toString( 0 ) : '';
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }
}

eZDataType::register( EZ_DATATYPESTRING_GMAPLOCATION, "ezgmaplocationtype" );

?>

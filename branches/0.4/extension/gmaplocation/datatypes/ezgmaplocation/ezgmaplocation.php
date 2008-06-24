<?php
//
// Definition of eZGmapLocation class
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

/*! \file ezgmaplocation.php
*/

/*!
  \class eZGmapLocation ezgmaplocation.php
  \ingroup eZDatatype
  \brief The class eZGmapLocation provides a datatype for storing 
  \latitude & longitude values.

*/

class eZGmapLocation
{
    /*!
     Constructor
    */
    function eZGmapLocation( $latitude, $longitude )
    {
        $this->Latitude = $latitude;
        $this->Longitude = $longitude;
    }

    /*!
     \return list of supported attributes
    */
    function attributes()
    {
        return array( 'latitude',
                      'longitude' );
    }

    function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    function &attribute( $name )
    {
        switch ( $name )
        {
            case "latitude" :
            {
                return $this->Latitude;
            }break;
            case "longitude" :
            {
                return $this->Longitude;
            }break;
            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", 'eZGmapLocation::attribute' );
                $retValue = null;
                return $retValue;
            }break;
        }
    }


    function decodeXML( $xmlString )
    {
        include_once( 'lib/ezxml/classes/ezxml.php' );

        $xml = new eZXML();


        $dom =& $xml->domTree( $xmlString );

        if ( $xmlString != "" )
        {
            $locationElement =& $dom->root( );

            $latitude = $locationElement->attributeValue( 'latitude' );
            $longitude = $locationElement->attributeValue( 'longitude' );

            $this->Latitude = $latitude;
            $this->Longitude = $longitude;

        }
        else
        {
            $this->Latitude = 0;
            $this->Longitude = 0;
        }
    }


    function &xmlString( )
    {
        include_once( 'lib/ezxml/classes/ezdomdocument.php' );

        $doc = new eZDOMDocument( "Location" );

        $root = $doc->createElementNode( "ezgmaplocation" );
        $root->appendAttribute( $doc->createAttributeNode( "latitude", $this->Latitude ) );
        $root->appendAttribute( $doc->createAttributeNode( "longitude", $this->Longitude ) );
        $doc->setRoot( $root );

        $xml = $doc->toString();

        return $xml;
    }

    function setLatitude( $value )
    {
        $this->Latitude = $value;
    }

    function setLongitude( $value )
    {
        $this->Longitude = $value;
    }


    var $Latitude;
    var $Longitude;
}

?>

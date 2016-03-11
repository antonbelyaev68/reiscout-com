(function ($) {
    var markerCluster;
    var clustersOnMap = new Array();
    var clusterListener;
    var gmap;

    function addClusterOnMap(cluster) {
        // Hide all cluster's markers
        $.each(cluster.getMarkers(), (function () {
            if (this.marker.isHidden == false) {
                this.marker.isHidden = true;
                this.marker.close();
            }
        }));

        var newCluster = new InfoBox({
            markers: cluster.getMarkers(),
            draggable: true,
            content: '<div class="clusterer"><div class="clusterer-inner">' + cluster.getMarkers().length + '</div></div>',
            disableAutoPan: true,
            pixelOffset: new google.maps.Size(-21, -21),
            position: cluster.getCenter(),
            closeBoxURL: "",
            isHidden: false,
            enableEventPropagation: true,
            pane: "mapPane"
        });

        cluster.cluster = newCluster;

        cluster.markers = cluster.getMarkers();
        cluster.cluster.open(map, cluster.marker);
        clustersOnMap.push(cluster);
    }

    function isClusterOnMap(clustersOnMap, cluster) {
        if (cluster === undefined) {
            return false;
        }

        if (clustersOnMap.length == 0) {
            return false;
        }

        var val = false;

        $.each(clustersOnMap, function (index, cluster_on_map) {
            if (cluster_on_map.getCenter() == cluster.getCenter()) {
                val = cluster_on_map;
            }
        });

        return val;
    }

    /**
     * Create google map.
     *
     * @param object map
     *   Object of map options.
     * @return object
     *   On success returns gmap object.
     */
    function gmap3ToolsCreateMap(map) {
        if (map.mapId == null) {
            console.log(Drupal.t('gmap3_tools error: Map id element is not defined.'));
            return null;
        }

        // backward compatibility
        if(map.clustering == undefined) {
            map.clustering = {
                enable: false
            }
        }

        // Create map.
        var markers = new Array();
        var mapOptions = map.mapOptions;

        mapOptions.center = new google.maps.LatLng(map.mapOptions.centerX, map.mapOptions.centerY);
        var gmap = new google.maps.Map(document.getElementById(map.mapId), mapOptions);

        // Array for storing all markers that are on this map.
        var gmapMarkers = [];
        var markersNum = 0;

        $.each(map.markers, function (i, markerData) {
            var position = new google.maps.LatLng(markerData.lat, markerData.lng);

            // creating Google Maps Marker
            var marker = new google.maps.Marker({
                position: position,
                map: gmap
            });

            // infobox
            var boxText = document.createElement("div");
            boxText.innerHTML = markerData.content;
            $(boxText).addClass("infobox");
            var myOptions = {
                content: boxText,
                disableAutoPan: false,
                maxWidth: 0,
                pixelOffset: new google.maps.Size(-145, -200),
                zIndex: null,
                boxStyle: {
                    width: "280px"
                },
                closeBoxMargin: "0px 0px 0px 0px",
                closeBoxURL: '/sites/default/themes/properta/img/icons/cross.png',
                infoBoxClearance: new google.maps.Size(1, 1),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: false
            };
            // storing content infobox inside google marker
            marker.infobox = new InfoBox(myOptions);
            marker.infobox.isOpen = false;

            // marker


            var extendMarkerOptions = {
                content: '<div class="marker ' + markerData.markerOptions.class + '"><div class="marker-inner"></div></div>',
                pixelOffset: new google.maps.Size(-21, -58),
                disableAutoPan: true,
                maxWidth: 0,
                position: position,
                closeBoxURL: "",
                isHidden: map.clustering.enable,
                // pane: "mapPane",
                enableEventPropagation: true
            };



            // storing infobox marker into Google Marker
            marker.marker = new InfoBox(extendMarkerOptions);
            marker.marker.open(gmap, marker);

            // add to collection - marker.infobox marker.marker
            markers.push(marker);

            google.maps.event.addListener(marker, "click", function (e) {
                var curMarker = this;

                $.each(markers, function (index, marker) {
                    // if marker is not the clicked marker, close the marker
                    if (marker !== curMarker) {
                        marker.infobox.close();
                        marker.infobox.isOpen = false;
                    }
                });

                if (curMarker.infobox.isOpen === false) {
                    curMarker.infobox.open(gmap, this);
                    curMarker.infobox.isOpen = true;
                    gmap.panTo(curMarker.getPosition());
                } else {
                    curMarker.infobox.close();
                    curMarker.infobox.isOpen = false;
                }

            });


            // Marker options.
            var markerOptions = $.extend({}, map.markerOptions, markerData.markerOptions);
            marker.setOptions(markerOptions);

            // Title of marker.
            if (typeof markerData.title != 'undefined') {
                marker.setTitle(markerData.title);
            }

            ++markersNum;
        });




        if (markersNum) {
            // Default markers position on map.
            if (map.gmap3ToolsOptions.defaultMarkersPosition == 'center') {
                gmap3ToolsCenterMarkers(gmap, map.markers, markersNum);
            }

            else if (map.gmap3ToolsOptions.defaultMarkersPosition == 'center zoom') {
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0; i < markersNum; i++) {
                    var marker = map.markers[i];

                    bounds.extend(new google.maps.LatLng(marker.lat, marker.lng));
                }
                gmap.fitBounds(bounds);
            }
        }

        // Store map object and map markers in map element so it can be accessed
        // later from js if needed.
        $('#' + map.mapId).data('gmap', gmap);
        $('#' + map.mapId).data('markers', markers);


        if(!map.clustering.enable) {
            return;
        }

        var markerCluster = new MarkerClusterer(gmap, markers, {
            gridSize: parseInt(map.clustering.gridSize),
            styles: [
            {
                height   : 36,
                width    : 36,
                textColor: 'transparent'
            }
        ]});

        google.maps.event.addListener(markerCluster, 'clusteringend', function (clusterer) {
            var availableClusters = clusterer.getClusters();

            $.each(clustersOnMap, function(index, clusterOnMap) {
                if(clusterOnMap.hasOwnProperty('cluster')) {
                    clusterOnMap.cluster.close();
                    clusterOnMap.cluster.isHidden_ = true;
                }
            });

            $.each(availableClusters, function (index, cluster) {

                if (cluster.getMarkers().length > 1) {
                    var newCluster = new InfoBox({
                        draggable             : true,
                        content               : '<div class="clusterer"><div class="clusterer-inner">' + cluster.getMarkers().length + '</div></div>',
                        disableAutoPan        : true,
                        pixelOffset           : new google.maps.Size(-21, -21),
                        position              : cluster.getCenter(),
                        closeBoxURL           : "",
                        isHidden              : false,
                        enableEventPropagation: true,
                        pane                  : "mapPane"
                    });

                    cluster.cluster = newCluster;
                    cluster.markers = cluster.getMarkers();
                    cluster.cluster.open(gmap, cluster.marker);
                    clustersOnMap.push(cluster);

                    $.each(cluster.getMarkers(), function (index, currentMarker) {
                        currentMarker.marker.close();
                    });

                } else {
                    $.each(cluster.getMarkers(), function (index, currentMarker) {
                        currentMarker.marker.open(gmap, currentMarker.marker);
                        currentMarker.marker.isHidden_ = false;
                    });
                }
            });
        });


    }

    /**
     * Center markers on map.
     */
    function gmap3ToolsCenterMarkers(map, markers, markersNum) {
        var centerLat = 0;
        var centerLng = 0;
        $.each(markers, function (i, markerData) {
            centerLat += parseFloat(markerData.lat);
            centerLng += parseFloat(markerData.lng);
        });
        centerLat /= markersNum;
        centerLng /= markersNum;
        map.setCenter(new google.maps.LatLng(centerLat, centerLng));
    }

    /**
     * Attach gmap3_tools maps.
     */
    Drupal.behaviors.gmap3_tools = {
        attach: function (context, settings) {
            // Create all defined google maps.
            if (Drupal.settings.gmap3_tools == undefined) {
                return;
            }
            $.each(Drupal.settings.gmap3_tools.maps, function (i, map) {
                // @todo - we should really use css selector here and not only element id.
                var $mapElement = $('#' + map.mapId, context);
                if ($mapElement.length == 0 || $mapElement.hasClass('gmap3-tools-processed')) {
                    return;
                }
                $mapElement.addClass('gmap3-tools-processed');
                gmap = gmap3ToolsCreateMap(map);
            });
        }
    };

})(jQuery);



var southWest, northEast;
var continentId, floorId;
var floors, dims;
var nodeLayers = new Object;
var hiddenLayers = JSON.parse(localStorage.hiddenLayers || "{}");
var showWaypoints = (localStorage.showWaypoints || false) == "true";
var showLandmarks = (localStorage.showLandmarks || false) == "true";
var waypointsChatcode = (localStorage.waypointsChatcode || "true") == "true";
var apiKey = (localStorage.apiKey || "");
var map;
var waypointsLayer;
var waypointsInitialized = false;
var landmarksLayer;
var landmarksInitialized = false;
var focusNode = false;

var clickedPosition;
var server = "0.0.0.1";
var lastAdditionId = null;

function unproject(coord) {
    return map.unproject(coord, map.getMaxZoom());
}

function displayWaypoints() {
    waypointsLayer.addTo(map);

    // If we haven't fetched waypoints before, do it now
    if (waypointsInitialized == false) {
        $.getJSON("/api/waypoints", function (data) {
            // Clear the current waypoints layer
            waypointsLayer.clearLayers();

            // Set the flag for initialization
            waypointsInitialized = true;

            // For every waypoint ...
            $.each(data, function (key, node) {
                // Make the icon
                icon = L.icon({
                    iconUrl: "https://render.guildwars2.com/file/32633AF8ADEA696A1EF56D3AE32D617B10D3AC57/157353.png",
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                });

                // Make the marker
                marker = L.marker(unproject([node.x, node.y]), {
                    icon: icon,
                    keyboard: false,
                    opacity: 1.0,
                    zIndexOffset: -1000
                });

                // Add a click event to marker
                marker.on('click', function (e) {
                    if (waypointsChatcode) {
                        window.prompt('Copy using Ctrl+C / Cmd+C. Then press enter to make this go away!', node.chatcode);
                    }
                });

                // Bind a label and add marker to the map
                marker.bindLabel(node.name);
                marker.addTo(waypointsLayer);
            });
        });
    }
}

function hideWaypoints() {
    map.removeLayer(waypointsLayer);
}

function displayLandmarks() {
    landmarksLayer.addTo(map);

    if (landmarksInitialized == false) {
        $.getJSON("/api/landmarks", function (data) {
            landmarksLayer.clearLayers();
            landmarksInitialized = true
            $.each(data, function (key, node) {

                icon = L.icon({
                    iconUrl: "https://render.guildwars2.com/file/25B230711176AB5728E86F5FC5F0BFAE48B32F6E/97461.png",
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                });

                marker = L.marker(unproject([node.x, node.y]), {
                    icon: icon,
                    keyboard: false,
                    opacity: 1.0,
                    zIndexOffset: -2000
                });

                marker.on('click', function (e) {
                    if (waypointsChatcode) {
                        window.prompt('Copy using Ctrl+C / Cmd+C. Then press enter to make this go away!', node.chatcode);
                    }
                });

                marker.bindLabel(node.name);
                marker.addTo(landmarksLayer);

            });
        });
    }
}

function hideLandmarks() {
    map.removeLayer(landmarksLayer);
}

// Add new data to the map
function mapData(data) {
    nodesMap.clearLayers();

    // Clear all layers
    $.each(nodeLayers, function (key, val) {
        val.clearLayers();
    });

    // Loop through all nodes
    $.each(data, function (key, node) {
        // Are there user notes attached to this node?
        var hasNotes = node.notes.length > 0;

        var container = containers[node.container_id] || {};

        // Create a node icon
        icon = L.icon({
            iconUrl: container.thumbnail,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            className: (node.is_permanent == 1 ? 'permanent-node' : (node.is_rich == 1 ? 'rich-node' : 'normal-node')) + (hasNotes ? ' node-with-notes' : '')
        });

        // Make a marker for the node
        marker = L.marker(unproject([node.x, node.y]), {
            icon: icon
        });

        // Check if we should be focussing on this node
        if (server && focusNode == node.id) {
            map.setView(unproject([node.x, node.y]), map.getMaxZoom());

            // Now remove the node parameter
            focusNode = false;
        }

        // Add the name and earnings as a label
        marker.bindLabel(container.name+" ("+currency(container.earnings)+")");

        // Add a click event
        marker.on('click', function (e) {
            $.getJSON("/api/node-info/" + node.id, function (data) {
                // Fill the modal with appropriate data
                $("#node-details-title").html("Node type: " + data.container.name);
                $("#node-notes").text(data.notes);
                $("#items-list").html("");

                if (data.is_permanent) {
                    $(".promote-button").hide();
                    $(".demote-button").hide();
                } else if (data.is_rich) {
                    $(".demote-button").show();
                    $(".promote-button").hide();
                } else {
                    $(".demote-button").hide();
                    $(".promote-button").show();
                }

                // Add item types found in this container to the modal
                $.each(data.container.items, function (key, val) {
                    $("#items-list").append(
                        "<div class = 'item-entry' data-key = '" + val.id + "'>"
                        + "<img src = '" + val.image_url + "' class = 'node-icon'>"
                        + "<span class = 'node-title'>" + val.name + "</span>"
                        + "<span class = 'node-price'>" + currency(val.price) + "</span></div>"
                    );
                });

                // Add a report button and show the modal
                $("#report-node-button, .promote-button, .demote-button").data('key', node.id);
                $("#node-details").modal('show');
            });
        });

        // Add the node to the appropriate layer
        var cid = node.container_id;

        // No node layer defined yet for this node type?
        if (nodeLayers[cid] == undefined) {
            // Create a layergroup
            nodeLayers[cid] = L.layerGroup();

            // Check to see if we should add the node
            if (hiddenLayers[cid] == undefined) {
                nodeLayers[cid].addTo(map);
            }
        }
        marker.addTo(nodeLayers[cid]);
    });
}

var mapRefresh = function(e) {
    bottomLeft = map.project(map.getBounds().getSouthWest(), map.getMaxZoom());
    topRight = map.project(map.getBounds().getNorthEast(), map.getMaxZoom());

    $.getJSON("/api/nodes/" + server, function (data) {
        mapData(data);
    });
};

function currency(coin) {
    var gold = Math.floor(coin / 10000);
    var silver = Math.floor( ( coin - gold * 10000) / 100);
    var copper = coin % 100;

    if (gold > 0) {
        return gold + "g " + silver + "s " + copper + "c";
    } else if (silver > 0) {
        return silver + "s " + copper + "c";
    } else {
        return copper + "c";
    }
}

function pickServer() {
    $("#server-dialog").modal({ keyboard: false, backdrop: 'static'});
    $("#serverselect").focus();
}

$(document).ready(function () {
    focusNode = getQueryVariable('node');

    $(document).on('click', '.node-entry', function () {
        $(".node-entry").removeClass('node-selected');
        $(this).addClass('node-selected');
    });

    $("#megaserver-warning").hide();

    $(document).on('click', '#add-node-button', function () {
        if ($(".node-selected").length == 1)
        {
            window.ignoreNext = true;

            $.post("/api/add-node", {
                'x': clickedPosition.x,
                'y': clickedPosition.y,
                'type': $(".node-selected").data('key'),
                'rich': $("#create-rich").prop('checked'),
                'notes': $("#create-notes").val(),
                'server': server
            }, function (data) {
                $("#add-node").modal('hide');
                $("#create-rich").prop('checked', false);
                $("#create-notes").val('');

                mapData(data.nodes);
            });
        }
    });

    $(document).on('click', '#report-node-button', function () {
        $.post("/api/report-node", {
            'id': $(this).data('key'),
            'server': server
        }, function (data) {
            $("#node-details").modal('hide');
            mapData(data.nodes);
        });
    });

    $(document).on('click', '.promote-button', function () {
        $.post("/api/promote-node", {
            'id': $(this).data('key'),
            'server': server
        }, function (data) {
            $("#node-details").modal('hide');
            mapData(data.nodes);
        });
    });

    $(document).on('click', '.demote-button', function () {
        $.post("/api/demote-node", {
            'id': $(this).data('key'),
            'server': server
        }, function (data) {
            $("#node-details").modal('hide');
            mapData(data.nodes);
        });
    });

    $(document).on('click', '#sidebar-items .item-entry', function () {
        if (apiKey.length > 0) {
            var cid = $(this).data('key');

            $("#ioi-results").html("<h4>Search Result</h4><p>Loading ...</p>");

            $.getJSON('/api/find-item/' + cid + '/' + apiKey, function (data) {
                $("#ioi-results").html("<h4>Search Result</h4>");

                $.each(data, function (key, value) {
                    $("#ioi-results").append("<p class = 'text-success'>" + value + "</p>");
                });

                if (data.length == 0) {
                    $("#ioi-results").append("Item was not found.");
                }
            });
        }
    });

    $("#sidebar-nodes .item-entry").each(function (index) {
        if (hiddenLayers[$(this).data('key')] != undefined) {
            $(this).addClass('disabled');
        }
    });

    $("#sidebar-nodes .node-earnings").each(function (index) {
        $(this).text(currency($(this).text()));
    });

    $(document).on('click', '#serverchange', pickServer);

    $(document).on('submit', '#changeserverform', function(e) {
        var tmpServer = $('#serverselect').val();
        if (tmpServer.substr(-2, 1) == ":") {
            tmpServer = tmpServer.substr(0, tmpServer.length - 2);
        }
        server = encodeURIComponent(tmpServer);
        e.preventDefault();
        if (server.length > 0) {
            $("#server-dialog").modal('hide');
            mapRefresh();
            $("#serverchange").text('Server (' + server + ')');

            if (history && history.pushState){
                history.replaceState(null, null, '/?server=' + server);
            }
        }
    });

    map = L.map('map', {
        minZoom: 2,
        maxZoom: 7,
        crs: L.CRS.Simple,
        attributionControl: false
    });

    map.zoomControl.setPosition('topright');

    southWest = unproject([1, 32767]);
    northEast = unproject([32767, 1]);

    southWestBounds = unproject([-32768, 65536]);
    northEastBounds = unproject([65536, -32768]);

    map.setMaxBounds(new L.LatLngBounds(southWestBounds, northEastBounds));

    L.tileLayer("https://tiles{s}.guildwars2.com/1/1/{z}/{x}/{y}.jpg", {
        minZoom: 2,
        maxZoom: 7,
        continuousWorld: true,
        subdomains: [1, 2, 3, 4],
        bounds: new L.LatLngBounds(southWest, northEast)
    }).addTo(map);

    map.on('contextmenu', function(e) {
        position = map.project(e.latlng, map.getMaxZoom());

        clickedPosition = position;

        $.getJSON("/api/whichmap/" + position.x + "/" + position.y, function (data) {
            if (data.error !== undefined) {
                return;
            }

            if (data.name === undefined) {
                return;
            }

            $("#map-title").html("Adding node to: " + data.name + " for server '" + server + "'");

            var listString = "";

            $("#list").html("");

            $.each(data.containers, function (key, val) {
                $("#list").append("<div class = 'node-entry' data-key = '" + val.id + "'><img src = '" + val.thumbnail + "' class = 'node-icon'><span class = 'node-title'>" + val.name + "</span></div>");
            });

            if (lastAdditionId != null && lastAdditionId != data.api_id)
            {
                $("#megaserver-warning").show();
            }
            else
            {
                $("#megaserver-warning").hide();
            }

            lastAdditionId = data.api_id;

            $("#add-node").modal('show');

        });
    });

    if (localStorage.mapZoom !== undefined && localStorage.mapCenter !== undefined) {
        map.setView(JSON.parse(localStorage.mapCenter), JSON.parse(localStorage.mapZoom));
    } else {
        map.fitBounds(new L.LatLngBounds(southWest, northEast));
        map.setZoom(map.getBoundsZoom(new L.LatLngBounds(southWest, northEast), true));
    }

    map.on('moveend', function (e) {
        var center = map.getCenter();
        var zoom = map.getZoom();

        localStorage.mapCenter = JSON.stringify([center.lat, center.lng]);
        localStorage.mapZoom = JSON.stringify(zoom);
    });

    map.on('zoomend', function (e) {
        var center = map.getCenter();
        var zoom = map.getZoom();

        localStorage.mapCenter = JSON.stringify([center.lat, center.lng]);
        localStorage.mapZoom = JSON.stringify(zoom);
    });

    nodesMap = L.layerGroup();
    nodesMap.addTo(map);

    // POIs and Waypoints
    landmarksLayer = L.layerGroup();

    if (showLandmarks) {
        displayLandmarks();
    }

    waypointsLayer = L.layerGroup();

    if (showWaypoints) {
        displayWaypoints();
    }

    $("#waypoints-pref").bootstrapSwitch({
        state: showWaypoints,
        size: 'mini',
        onSwitchChange: function (event, state) {
            showWaypoints = state;
            localStorage.showWaypoints = showWaypoints;

            if ( ! showWaypoints) {
                hideWaypoints();
            } else {
                displayWaypoints();
            }
        }
    });

    $("#landmarks-pref").bootstrapSwitch({
        state: showLandmarks,
        size: 'mini',
        onSwitchChange: function (event, state) {
            showLandmarks = state;
            localStorage.showLandmarks = showLandmarks;

            if ( ! showLandmarks) {
                hideLandmarks();
            } else {
                displayLandmarks();
            }
        }
    });

    $("#chatcodes-pref").bootstrapSwitch({
        state: waypointsChatcode,
        size: 'mini',
        onSwitchChange: function (event, state)
        {
            waypointsChatcode = state;
            localStorage.waypointsChatcode = waypointsChatcode;
        }
    });

    $("#api-key").val(apiKey);

    $("#api-key").on('change', function (event) {
        apiKey = $(this).val();
        localStorage.apiKey = apiKey;
    });

    if (getQueryVariable('server') !== undefined && getQueryVariable('server').length > 0) {
        server = getQueryVariable('server');
        mapRefresh();
        $("#serverchange").text('Server (' + server + ')');
    } else {
        pickServer();
    }

    if (top !== self) top.location.href = self.location.href;
});

// Hide a container layer from the map
function hideLayer(cid)
{
    if (nodeLayers[cid] !== undefined) {
        map.removeLayer(nodeLayers[cid]);
    } else {
        nodeLayers[cid] = L.layerGroup();
    }
}

// Show a container layer on the map
function showLayer(cid)
{
    if (nodeLayers[cid] !== undefined) {
        map.addLayer(nodeLayers[cid]);
    } else {
        nodeLayers[cid] = L.layerGroup();
        nodeLayers[cid].addTo(map);
    }
}

function getQueryVariable(variable)
{
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
            return pair[1];
        }
    }
}

(function () {
    $(document).on('click', '#showsidebar', function () {
        $(this).toggleClass("in");
        $("#sidebar").toggleClass('in');
    });

    $(document).on('click', '#shownavigation', function () {
        $(this).toggleClass("in");
        $(".Navigation__list").toggleClass('in');
    });
}());

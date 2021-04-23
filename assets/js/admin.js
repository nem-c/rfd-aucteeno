let catalogLocationMap;
let catalogLocationMarker;


jQuery(function ($) {
    let lastField;
    const dateTimePicker = new SimplePicker({
        zIndex: 999,
    });
    $('input.simplepicker').on('focus', function (event) {
        event.preventDefault();
        lastField = this;
        let current = $(lastField).val();
        if (0 < current.length) {
            dateTimePicker.reset(moment(current, 'YYYY-MM-DD\THH:mm:ss').toDate());
        } else {
            dateTimePicker.reset();
        }
        dateTimePicker.open();
    });

    dateTimePicker.on(
        'submit',
        function (date, readableDate) {
            $(lastField).val(moment(date).format('YYYY-MM-DD\THH:mm:ss'));
        }
    );

    if (0 < $('#catalog_location_map').length) {
        loadSingleCatalogMapbox($);
    }
    $('#location_to_catalog_map').on('click', function (event) {
        const button = $(this);
        event.preventDefault();

        const query = {
            street: $('#rfd_aucteeno_catalog_location_address').val(),
            city: $('#rfd_aucteeno_catalog_location_city').val(),
            postalcode: $('#rfd_aucteeno_catalog_location_postal_code').val(),
            state: $('#rfd_aucteeno_catalog_location_state').val(),
            country: $('#rfd_aucteeno_catalog_location_country_iso2').val()
        };

        const urlQuery = buildUrlQuery(query);

        button.prop('disabled', true);
        $.ajax({
            async: true,
            crossDomain: true,
            url: 'https://forward-reverse-geocoding.p.rapidapi.com/v1/forward?format=json&accept-language=en&limit=1&polygon_threshold=0.0&' + urlQuery,
            method: "GET",
            headers: {
                'x-rapidapi-key': 'e42cecc432msh36866256c810568p1d6af6jsnbe2458b331a8',
                'x-rapidapi-host': 'forward-reverse-geocoding.p.rapidapi.com'
            }
        }).success(function (response) {
            if (1 === response.length) {
                response = response[0];
                catalogLocationMap.flyTo(
                    {
                        center: [
                            response.lon,
                            response.lat
                        ]
                    }
                );
                catalogLocationMarker.setLngLat(
                    [
                        response.lon,
                        response.lat
                    ]
                );
                $('#rfd_aucteeno_catalog_location_latitude').val(parseFloat(response.lat).toFixed(6));
                $('#rfd_aucteeno_catalog_location_longitude').val(parseFloat(response.lon).toFixed(6));
            }
        }).done(function (response) {
            console.log(response);
            button.prop('disabled', false);
        });
    })
});

function loadSingleCatalogMapbox($) {
    let defaultLatitude = 44.787197;
    let defaultLongitude = 20.457273;

    const currentLatitude = parseFloat($('#rfd_aucteeno_catalog_location_latitude').val());
    const currentLongitude = parseFloat($('#rfd_aucteeno_catalog_location_longitude').val());

    if (0 === currentLatitude) {
        $('#rfd_aucteeno_catalog_location_latitude').val(defaultLatitude);
    } else {
        defaultLatitude = currentLatitude;
    }

    if (0 === currentLongitude) {
        $('#rfd_aucteeno_catalog_location_longitude').val(defaultLongitude);
    } else {
        defaultLongitude = currentLongitude;
    }

    mapboxgl.accessToken = 'pk.eyJ1IjoicmZkIiwiYSI6ImNra3F3MHZwdDA4NGEydnBjbDdiYno4d2sifQ.e5N4SP248gUcvSSR4-8E6w';
    catalogLocationMap = new mapboxgl.Map({
        container: 'catalog_location_map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [
            defaultLongitude,
            defaultLatitude
        ],
        zoom: 8
    });

    catalogLocationMarker = new mapboxgl.Marker(
        {
            draggable: true
        }
    );
    catalogLocationMarker.setLngLat(
        [
            defaultLongitude,
            defaultLongitude
        ]
    );
    catalogLocationMarker.addTo(catalogLocationMap);

    navigator.geolocation.getCurrentPosition(function (position) {
        catalogLocationMap.flyTo(
            {
                center: [
                    position.coords.longitude,
                    position.coords.latitude
                ]
            }
        );
        catalogLocationMarker.setLngLat(
            [
                position.coords.longitude,
                position.coords.latitude
            ]
        );
    });

    catalogLocationMap.on('load', function () {
        catalogLocationMap.resize();
    });

    function onDragEnd() {
        var lngLat = catalogLocationMarker.getLngLat();
        $('#rfd_aucteeno_catalog_location_latitude').val(parseFloat(lngLat.lat).toFixed(6));
        $('#rfd_aucteeno_catalog_location_longitude').val(parseFloat(lngLat.lng).toFixed(6));
    }

    catalogLocationMarker.on('dragend', onDragEnd);
}

buildUrlQuery = function (object) {
    let arguments = [];
    for (const item in object)
        if (object.hasOwnProperty(item)) {
            arguments.push(encodeURIComponent(item) + "=" + encodeURIComponent(object[item]));
        }
    return arguments.join("&");
}
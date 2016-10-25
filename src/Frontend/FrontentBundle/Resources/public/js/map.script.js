(function($) {
    $("#map-1").easymap();

    $("#map-2").easymap({
        size: ['100%', '300px'],
        control: {
            zoom: 4,
            center: [46.60611, 1.87528]
        }
    });

    $("#map-3").easymap({
        size: ['600px', '450px'],
        control: {
            zoom: 6,
            center: [46.60611, 1.87528],
            disableDefault: true,
            zoomControl: false,
            mapTypeControl: false
        }
    });

    $("#map-4").easymap({
        size: ['600px', '450px'],
        control: {
            zoom: 6,
            center: [46.60611, 1.87528]
        },
        markers: [
            {"latitude":47.614444,"longitude":1.366389,"ville":"BLOIS", "icone": "img/archery.png"},
            {"latitude":46.3333,"longitude":5.25,"ville":"MARBOZ"},
            {"latitude":48.2167,"longitude":-4.16667,"ville":"DINEAULT"},
            {"latitude":47.1667,"longitude":3.33333,"ville":"PREMERY"},
            {"latitude":43.604482,"longitude":1.443962,"ville":"TOULOUSE", "icone": "img/beachvolleyball.png"},
            {"latitude":48.856578,"longitude":2.351828,"ville":"PARIS", "icone": "img/boardercross.png"}
        ]
    });
})(jQuery);

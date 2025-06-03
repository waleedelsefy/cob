<?php
$points = get_post_meta(get_the_ID(), 'compound_polygon_points', true);

if (!is_array($points) || empty($points)) {
    return;
}

?>
<div id="compound-map" style="height: 400px; width: 100%; margin-bottom: 20px;"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('compound-map').setView([<?php echo $points[0][1]; ?>, <?php echo $points[0][0]; ?>], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var polygon = L.polygon([
            <?php foreach ($points as $coord) {
            echo '[' . $coord[1] . ', ' . $coord[0] . '],';
        } ?>
        ], {
            color: '#ff7800',
            weight: 3
        }).addTo(map);

        map.fitBounds(polygon.getBounds());
    });
</script>

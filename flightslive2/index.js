// Initialize and add the map
function initMap() {
    // BHX location
    const bhx = { lat: 52.452381, lng: -1.743507 };

    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 3,
        center: bhx,
    });
    
    // Replace example.com with your FQDN
    var rnum = Math.floor(Math.random() * 1000) + 1;    // random to work around Cloudflare cache
    var ctaLayer = new google.maps.KmlLayer({
        url: 'https://example.com/kml.zip?x=' + rnum,
        preserveViewport: true,
        clickable: true,
        map: map
    });
}
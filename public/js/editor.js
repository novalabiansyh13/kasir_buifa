/* global helper untuk token CSRF HRS-style
   encrypter / decrypter = base64 6x, invers dari base_encode()/base_decode() PHP */
function encrypter(teks) {
    var result = teks;
    for (var i = 0; i < 6; i++) {
        result = btoa(result);
    }
    return result;
}

function decrypter(teks) {
    var result = teks;
    for (var i = 0; i < 6; i++) {
        result = atob(result);
    }
    return result;
}

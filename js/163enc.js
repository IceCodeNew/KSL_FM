
function createSecretKey(KeySize) {
    var d, e, b = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        c = "";
    for (d = 0; KeySize > d; d += 1) e = Math.random() * b.length, e = Math.floor(e), c += b.charAt(e);
    return c
}

function aesEncrypt(text, secKey) {
    var c = CryptoJS.enc.Utf8.parse(secKey),
        d = CryptoJS.enc.Utf8.parse("0102030405060708"),
        e = CryptoJS.enc.Utf8.parse(text),
        f = CryptoJS.AES.encrypt(e, c, {
            iv: d,
            mode: CryptoJS.mode.CBC
        });
    return f.toString()
}

function rsaEncrypt(secKey, pubKey, modulus) {
    var d, e;
    return setMaxDigits(131), d = new RSAKeyPair(pubKey, "", modulus), e = encryptedString(d, secKey)
}

function asrsea(data){
// function asrsea(data, pubKey='', modulus='', nonce='') {
    modulus = '00e0b509f6259df8642dbc35662901477df22677ec152b5ff68ace615bb7b725152b3ab17a876aea8a5aa76d2e417629ec4ee341f56135fccf695280104e0312ecbda92557c93870114af6c9d05c4f7f0c3685b7a46bee255932575cce10b424d813cfe4875d3e82047b97ddef52741d546b8e289dc6935b3ece0462db0a22b8e7'
    nonce = '0CoJUm6Qyw8W8jud'
    pubKey = '010001';
    var h = {};
    var secKey = createSecretKey(16);
    h.encText = aesEncrypt(data, nonce);
    h.encText = aesEncrypt(h.encText, secKey);
    h.encSecKey = rsaEncrypt(secKey, pubKey, modulus);
    return h;
}

function createKeyAndEncSecKey(KeySize){
    modulus = '00e0b509f6259df8642dbc35662901477df22677ec152b5ff68ace615bb7b725152b3ab17a876aea8a5aa76d2e417629ec4ee341f56135fccf695280104e0312ecbda92557c93870114af6c9d05c4f7f0c3685b7a46bee255932575cce10b424d813cfe4875d3e82047b97ddef52741d546b8e289dc6935b3ece0462db0a22b8e7'
    // nonce = '0CoJUm6Qyw8W8jud'
    pubKey = '010001';
    var secKey = createSecretKey(KeySize);
    var encSecKey = rsaEncrypt(secKey, pubKey, modulus);
    var out={};
    out.secKey=secKey;
    out.encSecKey=encSecKey;
    return out;
}
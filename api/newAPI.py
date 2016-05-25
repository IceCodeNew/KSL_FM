from Crypto.Cipher import AES
import md5
import re
import os
import json
import requests
import hashlib
import random
import base64

modulus = '00e0b509f6259df8642dbc35662901477df22677ec152b5ff68ace615bb7b725152b3ab17a876aea8a5aa76d2e417629ec4ee341f56135fccf695280104e0312ecbda92557c93870114af6c9d05c4f7f0c3685b7a46bee255932575cce10b424d813cfe4875d3e82047b97ddef52741d546b8e289dc6935b3ece0462db0a22b8e7'
nonce = '0CoJUm6Qyw8W8jud'
pubKey = '010001'


def createSecretKey(size):
    return (''.join(map(lambda xx: (hex(ord(xx))[2:]), os.urandom(size))))[0:16]

def rsaEncrypt(text, pubKey, modulus):
    text = text[::-1]
    rs = int(text.encode('hex'), 16)**int(pubKey, 16)%int(modulus, 16)
    return format(rs, 'x').zfill(256)

def aesEncrypt(text, secKey):
    pad = 16 - len(text) % 16
    text = text + pad * chr(pad)
    encryptor = AES.new(secKey, 2, '0102030405060708')
    ciphertext = encryptor.encrypt(text)
    ciphertext = base64.b64encode(ciphertext)
    return ciphertext


#sids=[id1, id2]...
def get_songs_url(sids, bit_rate=192000):
    secKey = createSecretKey(16)
    encSecKey = rsaEncrypt(secKey, pubKey, modulus)
    session = requests.Session()
    action = 'http://music.163.com/weapi/song/enhance/player/url?csrf_token='
    # csrf = '2afc3af6ef8b0a6c1116c8af0f96796b'
    # action += csrf
    data = {
        "ids": sids,
        "br": bit_rate,
        # "csrf_token": csrf
    }
    text = json.dumps(data)
    encText = aesEncrypt(aesEncrypt(text, nonce), secKey)
    data = {
            'encSecKey': encSecKey,
            'params': encText,
    }

    connection = session.post(
        action,
        data=data,
    )
    if connection.status_code == 404:
        print 'This song is not available'
    if connection.status_code != 200:
        print 'get_song_url failed - wrong code'
        return false
    result = json.loads(connection.content)
    return result['data']


def get_playlist_info(pid):
    secKey = createSecretKey(16)
    encSecKey = rsaEncrypt(secKey, pubKey, modulus)
    session = requests.Session()
    action = 'http://music.163.com/weapi/playlist/detail?csrf_token='
    # csrf = '2afc3af6ef8b0a6c1116c8af0f96796b'
    # action += csrf
    data = {
        "id": pid,
        # "offset": 0,
        # "total": False,
        # "limit": 1000,
        # "n": 1000,
        # "csrf_token": csrf
    }
    text = json.dumps(data)
    encText = aesEncrypt(aesEncrypt(text, nonce), secKey)
    data = {
            'encSecKey': encSecKey,
            'params': encText,
    }

    connection = session.post(
        action,
        data=data,
    )
    if connection.status_code == 404:
        print 'This song is not available'
    if connection.status_code != 200:
        print 'get_song_url failed - wrong code'
        return False
    result = json.loads(connection.content)
    return result['result']


def parse_lyric(raw, slot_perSec=5):
    lyric={}
    pattern='\[(\d{0,2}):(\d{0,2})\.(\d{0,3})\](.*)'
    prog=re.compile(pattern)
    lines=raw.split('\n')
    for line in lines:
        result=prog.match(line)
        if result:
            mm=result.group(1)
            ss=result.group(2)
            ms=result.group(3)
            text=result.group(4)
            timeslot = int(60*int(mm) + int(ss) + float(ms)/float(1000))*slot_perSec
            lyric[timeslot]=text
    return lyric

        
def get_lyric(sid, slot_perSec=5):
    secKey = createSecretKey(16)
    encSecKey = rsaEncrypt(secKey, pubKey, modulus)
    session = requests.Session()
    action = 'http://music.163.com/api/song/lyric?os=pc&id={}&lv=-1&kv=-1&tv=-1'.format(sid)
    # csrf = '2afc3af6ef8b0a6c1116c8af0f96796b'
    # action += csrf

    connection = session.get(
        action,
        headers={
            'Referer': 'http://music.163.com/search/',
        }
    )
    if connection.status_code == 404:
        print 'This song is not available'
    if connection.status_code != 200:
        print 'get_song_url failed - wrong code'
        return False
    result = json.loads(connection.content)
    
    lyric={}
    olrc=''
    if 'lrc' in result:
        if 'lyric' in result['lrc']:
            olrc=result['lrc']['lyric']
    if olrc:
        olrc = parse_lyric(olrc, slot_perSec=slot_perSec)
    lyric['olrc']=olrc
    
    tlrc=''
    if 'tlyric' in result:
        if 'lyric' in result['tlyric']:
            tlrc=result['tlyric']['lyric']
    if tlrc:
        tlrc = parse_lyric(tlrc, slot_perSec=slot_perSec)
    lyric['tlrc']=tlrc
    return lyric

# print get_lyric_info(760955)
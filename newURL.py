# from config import config
from Crypto.Cipher import AES
import md5
import re
import os
import json
import requests
import hashlib
import random
import base64

def aesEncrypt(text, secKey):
    pad = 16 - len(text) % 16
    text = text + pad * chr(pad)
    encryptor = AES.new(secKey, 2, '0102030405060708')
    ciphertext = encryptor.encrypt(text)
    ciphertext = base64.b64encode(ciphertext)
    return ciphertext

def rsaEncrypt(text, pubKey, modulus):
    text = text[::-1]
    rs = int(text.encode('hex'), 16)**int(pubKey, 16)%int(modulus, 16)
    return format(rs, 'x').zfill(256)

def createSecretKey(size):
    return (''.join(map(lambda xx: (hex(ord(xx))[2:]), os.urandom(size))))[0:16]


#sids=[id1, id2]...
def get_songs_url(sids, bit_rate=192000):
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
    # print data

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
    # return result
    return result['data']


modulus = '00e0b509f6259df8642dbc35662901477df22677ec152b5ff68ace615bb7b725152b3ab17a876aea8a5aa76d2e417629ec4ee341f56135fccf695280104e0312ecbda92557c93870114af6c9d05c4f7f0c3685b7a46bee255932575cce10b424d813cfe4875d3e82047b97ddef52741d546b8e289dc6935b3ece0462db0a22b8e7'
nonce = '0CoJUm6Qyw8W8jud'
pubKey = '010001'
secKey = createSecretKey(16)
encSecKey = rsaEncrypt(secKey, pubKey, modulus)
session = requests.Session()


totalmp3data=[]
for filename in os.listdir('/Users/mzc/Documents/Github/KSL_FM/playlist_cache/'):
    if filename.find('.json')<0:
        continue
    # print filename
    rdata=json.load(open(filename))
    data=rdata['result']['tracks']
    sids=[]
    for track in data:
        sids.append(track['id'])
    
    mp3data=get_songs_url(sids, bit_rate='192000')
    totalmp3data.extend(mp3data)

outfile=open('output.json', 'w')
output={}
for track in totalmp3data:
    trackinfo={}
    # trackinfo['br']=track['br']
    trackinfo['urlm']=track['url']
    # trackinfo['br']=track['br']
    output[track['id']]=trackinfo
json.dump(output, outfile)

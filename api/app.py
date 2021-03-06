#!/usr/bin/python
from flask import abort
from flask import Flask, jsonify
from flask import make_response
import datetime
import random
import newAPI as music163
import playlist
import pickle
import json
from functools import wraps
from flask import redirect, request, current_app

app = Flask(__name__)
cacheSong={}
cachePlaylist={}
cacheLyric={}
KSLdict=playlist.read_playlist()

def support_jsonp(f):
    """Wraps JSONified output for JSONP"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        callback = request.args.get('callback', False)
        if callback:
            content = str(callback) + '(' + str(f(*args,**kwargs).data) + ')'
            return current_app.response_class(content, mimetype='application/javascript')
        else:
            return f(*args, **kwargs)
    return decorated_function


@app.route('/')
def index():
    return "Hello, World!"


@app.route('/test', methods=['GET'])
@support_jsonp
def test():
    return jsonify({"foo":"bar"})


@app.route('/song/<int:sid>/<int:br>', methods=['GET'])
@app.route('/song/<int:sid>/', methods=['GET'])
@app.route('/song/<int:sid>', methods=['GET'])
def get_song(sid, br=192, output_format='json', secKey=None, encSecKey=None):
    data={}
    if sid in cacheSong:
        if br in cacheSong[sid]:
            if 'expire' in cacheSong[sid][br]:
                if cacheSong[sid][br]['expire']>datetime.datetime.now():
                    data=cacheSong[sid][br]
    if not data:
        data = music163.get_songs_url([sid], bit_rate=br*1000, secKey=secKey, encSecKey=encSecKey)
        if len(data)==0:
            abort(404)
        if not data[0]['url']:
            abort(404)
        data = data[0]
        if sid not in cacheSong:
            cacheSong[sid]={}
        cacheSong[sid][br]=data
        cacheSong[sid][br]['expire']=datetime.datetime.now()+datetime.timedelta(seconds=data['expi'])
    
    output_data={}
    output_data['url'] = data['url']
    output_data['br'] = data['br']
    output_data['size'] = data['size']
    output_data['sid'] = sid
    
    if output_format=='inner':
        return output_data
    else:
        return jsonify(**output_data)


@app.route('/lyric/<int:sid>/<int:slot_perSec>/', methods=['GET'])
@app.route('/lyric/<int:sid>/<int:slot_perSec>', methods=['GET'])
@app.route('/lyric/<int:sid>/', methods=['GET'])
@app.route('/lyric/<int:sid>', methods=['GET'])
def get_lyric(sid, slot_perSec=5, output_format='json'):
    data={}
    if sid in cacheLyric:
        if slot_perSec in cacheLyric[sid]:
            data=cacheLyric[sid][slot_perSec]
    if not data:
        data = music163.get_lyric(sid, slot_perSec=slot_perSec)
        if sid not in cacheLyric:
            cacheLyric[sid]={}
        cacheLyric[sid][slot_perSec] = data
        if 'olrc' not in data or 'tlrc' not in data:
            abort(404)
            
    if output_format=='inner':
        return data
    else:
        return jsonify(**data)


@app.route('/playlist/<int:pid>/', methods=['GET'])
@app.route('/playlist/<int:pid>', methods=['GET'])
def get_playlist(pid, output_format='json'):
    data={}
    if pid in cachePlaylist:
        data=cachePlaylist[pid]
    else:
        data = music163.get_playlist_info(pid)
        print 'aa'
        if 'tracks' not in data:
            abort(404)
        
        output_data={}
        output_data['coverImgUrl']=data['coverImgUrl']
        output_data['pid']=data['id']
        output_data['pl_name']=data['name']
        output_data['trackCount']=data['trackCount']
        output_tracks=[]
        for track in data['tracks']:
            output_track={}
            output_track['album']=track['album']['name']
            output_track['artist']=track['artists'][0]['name']
            output_track['title']=track['name']
            output_track['id']=track['id']
            output_tracks.append(output_track)
        output_data['tracks']=output_tracks
        
        data=output_data
        cachePlaylist[pid]=output_data
        
    if output_format=='inner':
        return data
    else:
        return jsonify(**data)

# @app.route('/KSL/<KSLid>/<int:br>/', methods=['GET'])
# @app.route('/KSL/<KSLid>/<int:br>', methods=['GET'])
# @app.route('/KSL/<KSLid>/', methods=['GET'])
# @app.route('/KSL/<KSLid>', methods=['GET'])
# @app.route('/KSL/<int:br>/', methods=['GET'])
# @app.route('/KSL/<int:br>', methods=['GET'])
# Read encSecKey from client, to speed up server response
@app.route('/KSL/', methods=['POST'])
@app.route('/KSL', methods=['POST'])
def get_KSL():
    if not request.json:
        abort(404)
        
    if 'encSecKey' not in request.json or 'secKey' not in request.json:
        abort(404)
    if 'br' not in request.json:
        br=192
    else:
        br=int(request.json['br'])
        
    if 'KSLid' not in request.json:
        KSLid = random.choice(KSLdict.keys())
    else:
        if not request.json['KSLid']:
            KSLid = random.choice(KSLdict.keys())
        else:
            if request.json['KSLid'] in KSLdict:
                KSLid = request.json['KSLid']
            else:
                abort(404)
    pid = KSLdict[KSLid]

    pl_data =get_playlist(pid, output_format='inner')
    if pl_data['trackCount']>1:
        random_index = random.randint(0, pl_data['trackCount']-1)
    else:
        random_index=1
    song_data = pl_data['tracks'][random_index]
    
    output_data={}
    output_data['cover'] = 'img/%s.jpg' %KSLid
    output_data['album'] = song_data['album']
    output_data['artist'] = song_data['artist']
    output_data['title'] = song_data['title']
    detail_song_data = get_song(song_data['id'], br=br, output_format='inner', secKey=request.json['secKey'], encSecKey=request.json['encSecKey'])
    output_data['url'] = detail_song_data['url']
    output_data['br'] = detail_song_data['br']
    output_data['ksl_id'] = KSLid
    output_data['sid'] = song_data['id']
    lyric = get_lyric(song_data['id'], output_format='inner')
    output_data['olrc'] = lyric['olrc']
    output_data['tlrc'] = lyric['tlrc']
    
    return jsonify(output_data)


@app.errorhandler(404)
def not_found(error):
    return make_response(jsonify({'error': 'Not found'}), 404)
        
        
if __name__ == '__main__':

    try:
        with open('/var/www/kslm/api/cache.db', 'r') as cacheFile:
            cachePlaylist, cacheLyric = pickle.load(cacheFile)
    except:
        print 'not found cache'
        # for kid, pid in sorted(KSLdict.items()):
        #     print kid
        #     pl_data=get_playlist(pid, output_format='inner')
        #     for track in pl_data['tracks']:
        #         song_data=get_lyric(track['id'], output_format='inner')
        # cacheFile=open('cache.db', 'wb')
        # pickle.dump((cachePlaylist, cacheLyric), cacheFile)
    app.run(debug=True)


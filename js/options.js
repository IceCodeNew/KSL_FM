jQuery(document).ready(function ($) {

    br = getBitRate();
    resetBitRateButton(br);
    $('.br-btn').click(function(){
        br=this.id.replace('brBtn', '');
        resetBitRateButton(br);
        setBitRate(br);
    });

    black_sid = localStorage.getItem("kslm_blacksid");
    black_sid = black_sid ? JSON.parse(black_sid) : {};

    black_sid_table=[]
    for (var key in black_sid){
        black_sid[key]["delete"]="<a class='delete-button' id='sid"+key+"';>删除</a>";
        black_sid_table.push(black_sid[key])
    }

    dynatable = $('#my_table').dynatable({
      dataset: {
        records: black_sid_table,
      },
      features: {
        paginate: false,
        recordCount: false,
        search: false,
        perPageSelect: false
      },

    }).data('dynatable');
    // dynatable.sorts.add('rank', 1)
    dynatable.process();

    $('.delete-button').click(function(){
        deleteBlacklist(this.id.replace('sid',''));
    });
    $('.delete-all-button').click(function(){
        deleteBlacklist();
    });
});



function deleteBlacklist(item_key){
    if(typeof(Storage) === "undefined") {
        return;
    }

    if(typeof(item_key)==="undefined"){
        localStorage.setItem("kslm_blacksid","{}");
        location.reload();
        return;
    }

    black_sid = localStorage.getItem("kslm_blacksid")
    black_sid = black_sid ? JSON.parse(black_sid) : {};

    if(item_key in black_sid){
        delete black_sid[item_key];
    }
    localStorage.setItem("kslm_blacksid", JSON.stringify(black_sid));
    
    location.reload();
    // dynatable.dom.update;
};

function setBitRate(br){
    if(typeof(Storage) === "undefined" || br===null) {
        return;
    }
    if(br === ''){
        br=192;
    }
    br = localStorage.setItem("bitrate", br);
}

function resetBitRateButton(br){
    brStatus=[0, 0, 0];
    brBtnID=['#128brBtn', '#192brBtn', '#320brBtn'];
    if(br<=128){
        brStatus[0]=1;
    }
    else{
        if(br>=320){
            brStatus[2]=1;
        }
        else{
            brStatus[1]=1;
        }
    }

    for (var i = 0; i<brStatus.length ; i++) {
        if(brStatus[i]>0){
            $(brBtnID[i]).addClass("btn-success");
        }
        else{
            if($(brBtnID[i]).hasClass("btn-success")){
                $(brBtnID[i]).removeClass("btn-success");
            }
        }
    }
}

function getBitRate(){
    default_br=192;
    if(typeof(Storage) === "undefined") {
        br = default_br;
    }
    br = localStorage.getItem("bitrate");
    br = br ? br : default_br;
    return br;
}



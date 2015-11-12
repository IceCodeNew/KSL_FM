jQuery(document).ready(function ($) {
    
    black_sid = localStorage.getItem("kslm_blacksid")
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



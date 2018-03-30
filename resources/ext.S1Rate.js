var apiPath = '/api.php';
var pageid = mw.config.get('wgArticleId');
var lastScoreDate;

function fetchResult(){
    $.ajax({
        url: apiPath,
        type: 'GET',
        data: {
            action: 'GetPageScore',
            format: 'json',
            pageid: pageid
        },
        datatype: 'json'
    }).done (function( response ) {
        if ( response.code ) {
            console.log( response.message );
            return;
        }

        var results = Object.values(response.data.results)
        var totolCount = results.reduce(function(x, y){
            return parseInt(x) + parseInt(y)
        })

        var meters = $('.meter')
        meters.map(function (index, domElement) {
            var d = $(domElement)
            d.animate({'width': results[index] / totolCount * 100 + '%'})

        })

        var percents = $('.percent')
        percents.map(function(index, domElement){
            domElement.innerText = (results[index] / totolCount * 100).toFixed(1) + '% (' + results[index] + ')'
        })
        
        $('#s1rateform .totalcount').text('共有 '+ totolCount +' 人参与评分')

    }).fail(function() {
        console.log( 'connection error' );
    })
}

function fetchMyRate() {
    $.ajax({
        url: apiPath,
        type: 'GET',
        data: {
            action: 'GetUserScore',
            format: 'json',
            pageid: pageid,
            target: mw.config.get('wgUserName')
        },
        datatype: 'json'
    }).done (function(response) {
        if ( response.code || response.error) {
            console.log( response );
            return;
        }
        var data = response.data;
        $('input[type="radio"][name="s1rateoption"][value='+ data.lastScore + ']').attr("checked","checked");
        if( new Date().getTime() / 1000 - data.date < RateInterval ){
            $('#s1rateform input:radio').attr('disabled',true);
        }
        lastScoreDate = data.date;

    }).fail(function() {
        console.log( 'connection error' );
    })
}

function fetchToken(){
    $.ajax({
        url: apiPath,
        type: 'GET',
        data: {
            action: 'query',
            format: 'json',
            meta: 'tokens',
            type: 'csrf'
        },
        datatype: 'json'
    }).done (function(response) {
        var token = response.query.tokens.csrftoken;
        $('#s1rateform').attr('token', token);
    }).fail(function() {
        console.log( 'connection error' );
    })
}

function submitData(){
    var token = $('#s1rateform').attr('token');
    var score = $('input:radio[name="s1rateoption"]:checked').attr('value');
    if (!score){
        $('#commonModal').text('请选择有效选项')
        $('#commonModal').foundation('reveal', 'open');
        return false;
    }

    $.ajax({
        url: apiPath,
        type: 'POST',
        data: {
            action: 'RatePage',
            format: 'json',
            pageid: pageid,
            score: score,
            token: token
        },
        datatype: 'json'
    }).done (function(response) {
        console.log(response);
        fetchResult();
        fetchMyRate();
    })
}

function modalControl(){
    if (!mw.config.get('wgUserId')){
        $('#commonModal').text('您尚未登录，请登录后再评分')
        $('#commonModal').foundation('reveal', 'open');
        return false;
    }else if (lastScoreDate){
        if ( new Date().getTime() / 1000 - lastScoreDate < RateInterval ){
            $('#commonModal').html('您已经参与过评分<br>' + parseInt(RateInterval - new Date().getTime() / 1000 + parseInt(lastScoreDate)) + '秒后可以修改评分')
            $('#commonModal').foundation('reveal', 'open');
            return false;
        }else{
            $('#askModal').foundation('reveal', 'open');
            return false;
        }
    }else{
        submitData()
    }
}

$('#s1rateform').submit(
    function(){
        modalControl()
        return false
    }
)
$('#askModal button[name=yes]').click(
    function(){
        submitData()
        $('#askModal').foundation('reveal', 'close');
    }
)
$('#askModal button[name=no]').click(
    function(){
        $('#askModal').foundation('reveal', 'close');
    }
)

fetchToken();
fetchResult();
fetchMyRate();
var apiPath = '/api.php';
var pageid = mw.config.get('wgArticleId');

function fetchData(){
    $.ajax({
        url: apiPath,
        type: 'GET',
        data: {
            action: 'GetPageScore',
            format: 'json',
            pageid: pageid
        },
        datatype: 'json'
        })
        .done (function( response ) {
            if ( response.code ) {
                console.log( response.message );
                return;
            }
            for(var i = 1; i <= 5; i++){
                $('#sri' + i).text(response.data['item'+i])
            }
        })
        .fail(function() {
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
        var data = response.data;
        $('input[type="radio"][name="s1rateoption"][value='+ data.lastScore + ']').attr("checked","checked");
        if( new Date().getTime() / 1000 - data.date < RateInterval ){
            $('#s1rateform input:radio').attr('disabled',true);
        }
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
    })
}

function submitData(){
    var token = $('#s1rateform').attr('token');
    var score = $('input:radio[name="s1rateoption"]:checked').attr('value');
    
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
        fetchData();
        fetchMyRate();
    })
}

$('#s1rateform').submit(
    function(){
        submitData();
        return false
    }
)

fetchToken();
fetchMyRate();
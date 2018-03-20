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
            console.log(response)
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
// alert("Submitted");

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
        console.log(token);
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
    })
}

$('#s1rateform').submit(
    function(){
        submitData();
        return false
    }
)

fetchToken();
// fetchData();
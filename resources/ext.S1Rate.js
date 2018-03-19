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

// fetchData();
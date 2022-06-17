function create_token(date, hash, csrf) {

    if (date=='' || hash=='' || csrf=='') {
        alert('잘못된 형식입니다.');
        return false;
    }

    if (!confirm("핀 코드를 추출하시겠습니까?")) {
        alert('취소하였습니다.');
        return false;
    } else {
        $.ajax({
            url: "./action/create_token.php",
            type:"post",
            data:{"date":date, "hash":hash, "csrf":csrf},
            dataType: 'json',
            async:false,
            // cache : false,
            success: function(data){ 
                if(data.status == 1) {
                    alert('성공');
                    location.reload();
                } else if(data.status == 2){
                    alert(data.data);
                    location.reload();
                } else {
                    alert(data.msg);
                }
            },
            error: function (err) {
                console.log("just only error!! : " + err);
            }
        });
    }

}
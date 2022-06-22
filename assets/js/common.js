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

function form_check(f) {
    let price = f.price.value;
    let hap = f.hap.value;
    let __csrfToken = f._csrfToken.value;

    if (f.price.value == '') {
        alert("추출할 QR핀코드 금액을 입력해주세요.");
        return false;
    }

    price = Math.floor(price/1000) * 1000;
    if (price == 0) {
        alert("금액은 천 단위만 입력이 가능합니다.");
        return false;
    }

    if (price > hap) {
        alert("잔액이 부족합니다.");
        return false;
    }

    if (!confirm("핀 코드를 추출하시겠습니까?")) {
        alert('취소하였습니다.');
        return false;
    } else {
        $.ajax({
            url: "./action/create_share_token.php",
            type:"post",
            data:{"price":price, "csrf":__csrfToken},
            dataType: 'json',
            async:false,
            // cache : false,
            success: function(data){ 
                if(data.status == 1) {
                    alert('성공');
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
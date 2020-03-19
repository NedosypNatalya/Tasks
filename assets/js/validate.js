function validateRegister(){
    valid = true;

    name = $("#name").val();
    surname = $("#surname").val();
    email = $("#email").val();
    login = $("#login").val();
    password = $("#password").val();
    confirm = $("#confirm").val();
    if(name == "" || surname == "" || email == "" || login == "" || password == "" || confirm == ""){
        alert("Пожалуйста, заполните все поля!");
        valid = false;
    }else{
        if(password != confirm){
            alert("Пароли не совпадают!");
            valid = false;
        }else{
            if(password.length<=5){
                alert("Слишком маленький пароль!");
                valid = false;
            }
        }
    }

    return valid;
}

function validateSignin(){
    valid = true;

    login = $("#login").val();
    password = $("#password").val();
    if(login == "" || password == ""){
        alert("Пожалуйста, заполните все поля!");
        valid = false;
    }
    return valid;
}
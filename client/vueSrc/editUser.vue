<template>
<div>
    <div >
    <button type="button" class="btn btn-success home"
        v-on:click="home" >Home</button>
    <!-- </div>
    <div class="navBar"> -->
    <button type="button" class="btn btn-success home"
        v-on:click="back" >Back</button>
    <button type="button" class="btn btn-danger logout"
        v-on:click="logout" >Logout</button>
    </div>
     <div class="login">
            <div class="dataBlock">
                <div>
                    <div class="form-group" >
                        <label class="control-label" for="login">Name</label>
                        <input v-model="name"
                        class="form-control" type="text" placeholder="Name">
                        <div class="error">{{errorMsgs.nameErr}}</div>
                    </div>

                    <div class="form-group" >
                        <label class="control-label" for="password">Email</label>
                        <input v-model="email"
                        class="form-control" type="text" placeholder="Email">
                        <div class="error">{{errorMsgs.emailErr}}</div>
                    </div>

                    <div class="form-group" >
                        <label class="control-label" for="login">Login</label>
                        <input v-model="login"
                        class="form-control" type="text" placeholder="Login">
                        <div class="error">{{errorMsgs.loginErr}}</div>
                    </div>

                    <div class="form-group" >
                        <label class="control-label" for="password"> New Password</label>
                        <input v-model="password"
                        class="form-control" type="text" placeholder="Password">
                    </div>
                </div>
            </div>

            <button class="btn btn-success checkLogin" v-on:click="checEdit" type="submit">Edit</button>
    </div>
  </div>
</template>

<script>

export default {
    data() {
        return {
            login:'',
            password: '',
            name: '',
            email: '',
            errorMsgs: {
                loginErr: '',
                nameErr: '',
                emailErr: ''
            }
        }
    },
    methods: {
        home: function () {
            this.$router.go(-2);
        },
        back: function () {
            this.$router.go(-1);
        },
        logout: function () {
            this.$parent.logout();
        },
        checEdit: function(){
            var self = this;
            if(this.name == ''){
                this.errorMsgs.nameErr = REQUIRED_FIELD;
            } else {
                this.errorMsgs.nameErr = ''
            }

            if(this.email == ''){
                this.errorMsgs.emailErr = REQUIRED_FIELD;
            } else {
                var result = this.email.match( /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i );
                if(result == null){
                    this.errorMsgs.emailErr = WRONG_EMAIL;
                } else {
                    this.errorMsgs.emailErr = ''
                }
            }

            if(this.password == ''){
                this.errorMsgs.passwordErr = REQUIRED_FIELD;
            } else {
                this.errorMsgs.passwordErr = ''
            }

            if(this.login == ''){
                this.errorMsgs.loginErr = REQUIRED_FIELD;
            } else {
                this.errorMsgs.loginErr = ''
            }
            
            if(this.errorMsgs.loginErr == '' && this.errorMsgs.emailErr == '' 
            && this.errorMsgs.nameErr == ''){
                fetch(USER_URL, {
                    method: 'put',
                    headers: {  
                        "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
                    },
                    credentials: "include",
                    body: 'name='+JSON.stringify(this.name)+'&login='+JSON.stringify(this.login)
                    +'&password='+JSON.stringify(this.password)+'&email='+JSON.stringify(this.email)
                    +'&id='+JSON.stringify(this.$route.params.userId)
                })
                .then(this.$parent.status)
                .then(this.$parent.json)
                .then(function (data) {
                    if(data == true) {
                        alert(USER_SUCCESS_EDIT);
                        self.$router.go(-1);
                    } else {
                        alert(USER_EDIT_ERR);
                    }

                });
            }
        }
    },
    created () {
        this.$parent.checkAuth();
        var self = this;
        fetch(USER_URL+this.$route.params.userId,{  
        method: 'get',
        credentials: "include"
        })
        .then(this.$parent.status)
        .then(this.$parent.json)
        .then(function(data){
            if(data != false){
                self.login = data[0].login;
                self.name = data[0].name;
                self.email = data[0].email;
            }     
      });
    }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.login{
    margin-left: 35%;
    margin-right: 35%;
    text-align: center;
}
.dataBlock {
        height: 350px;
    width: 400px;
        background-color: #90EE90;
    text-align: center;
    border: 1px solid gray;
    border-radius: 5px;
}
.checkLogin {
    width: 300px;
    margin-top: 15px;
}
.dataBlock > div {
            height: 320px;
    width: 260px;
    margin: auto;
    border: 1px solid gray;
    border-radius: 5px;
    background-color: #FAFAD2;
}
.dataBlock > div > div {
    margin-left: 5px;
    margin-right: 5px;
}
.error {
    color:red;
    height: 20px;
}
.form-group{
    margin-bottom: 0;
}
</style>

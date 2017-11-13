<template>
     <div class="login">
            <div class="dataBlock">
                <div>
                    <div class="form-group" >
                        <label class="control-label" for="login">Login</label>
                        <input v-model="login"
                        v-bind:style="loginErrorStyle"
                        class="form-control" type="text" placeholder="Login">
                    </div>

                    <div class="form-group" >
                        <label class="control-label" for="password">Password</label>
                        <input v-model="password"
                        v-bind:style="passwordErrorStyle"
                        class="form-control" type="password" placeholder="Password">
                    </div>
                </div>
            </div>

            <button class="btn btn-success checkLogin" v-on:click="checkAuth" type="submit">Login</button>
    </div>
  </div>
</template>

<script>

export default {
    data() {
        return {
            login:'',
            password: '',
              loginErrorStyle: {
            },
              passwordErrorStyle: {
            }
        }
    },
    methods: {
        checkAuth: function(){
            var self = this;
            if(this.login == ''){
                this.loginErrorStyle = {'border-color': 'red'}
            } else {
                this.loginErrorStyle = {}
            }

            if(this.password == ''){
                this.passwordErrorStyle = {'border-color': 'red'}
            }else {
                this.passwordErrorStyle = {}
            }

            if(this.login != '' && this.password !=''){
                fetch(AUTH_URL, {
                    method: 'put',
                    headers: {  
                        "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
                    },
                    credentials: "include",
                    body: 'login='+JSON.stringify(this.login)+'&password='+JSON.stringify(this.password)
                })
                .then(this.$parent.status)
                .then(this.$parent.json)
                .then(function (data) {
                    if(data.err == null){
                        self.$parent.role = data.role;
                        var date = new Date(new Date().getTime() + 60 * 1000);
                        self.$router.push({ path: '/mainpage/calendar/1'});
                    } else {
                        alert(data.err);
                    }
                });
            }

        }
    },
    created () {
        if(this.$parent.getCookie('role') != undefined 
        && this.$parent.getCookie('id') != undefined
        && this.$parent.getCookie('hash') != undefined){
            this.$router.push({ path: '/mainPage/calendar/1'});
        }
    }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.login{
    margin-left: 40%;
    margin-right: 40%;
    text-align: center;
}
.dataBlock {
        height: 200px;
    width: 300px;
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
            height: 170px;
    width: 260px;
    margin: auto;
    margin-top: 15px;
    border: 1px solid gray;
    border-radius: 5px;
    background-color: #FAFAD2;
}
.dataBlock > div > div {
    margin-left: 5px;
    margin-right: 5px;
    margin-top: 10px;
    margin-bottom: 20px;
}
</style>

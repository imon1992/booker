<template>
<div>
    <div >
      <button type="button" class="btn btn-success home"
          v-on:click="home" >Home</button>
        <button type="button" class="btn btn-danger logout"
        v-on:click="logout" >Logout</button>
    </div>
  <div class="container ">
    <div class="col-md-3"></div>
    <div class="col-md-6 usersContent">
        <div v-for="user in users"
        class="userAct">
            <div class="user">
                <p>
                    <a :href="`mailto:${user.email}`"> {{user.name}} </a>
                </p>
            </div>
            <span v-if="user.isActive=='active'"class="active">{{user.isActive}}</span>                
            <span v-else class="noActive">{{user.isActive}}</span>                

                <button class="btn btn-info" v-on:click="editUser(user.id)">Edit</button>
                <button v-if="user.isActive == 'active'"class="btn btn-danger"
                v-on:click="removeUser(user.id)">Remove</button>
      
        </div>

    </div>
    <div class="col-md-3"></div>
  </div>
    <div class="addUser">
        <button class="btn btn-primary"
        v-on:click="addNewUser">Add New User</button>
    </div>
</div>
</template>

<script>
export default {
  name: 'app',
  data () {
    return {
        users: ''
    }
  },
  methods: {
    addNewUser: function () {
        this.$router.push({ path: '/addNewUser'});
    },
    editUser: function (userId) {
        this.$router.push({ path: '/editUser/'+userId});
    },
    removeUser: function (userId) {
        var self = this;
        var date = new Date();
        var month = date.getMonth();
        var day = date.getDate();
        var fromDate;
        month = month +1;
        if(month.toString().length == 1) {
            month = '0'+month;
        }
        if(day.toString().length == 1) {
            day = '0' + day;
        }
        fromDate = date.getFullYear() + '-' + month + '-' + day;

        fetch(USER_URL, {
            method: 'delete',
            headers: {  
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"  
            },  
            credentials: "include",
            body: 'date='+JSON.stringify(fromDate)
            +'&userId='+JSON.stringify(userId)
        })
        .then(this.$parent.status)
        .then(this.$parent.json)
        .then(function (data) {
            if(data == true){
                alert(USER_SUCCESS_DELETED);
                self.getUsers();
            } else {
                alert(USER_DELETE_ERR);
            }
        });
    },
    getUsers: function () {
        var self = this;
        fetch(USER_URL,{  
            method: 'get',
            credentials: 'include'
        })
        .then(self.$parent.status)
        .then(self.$parent.json)
        .then(function(data){
            if(data != false){
                self.users = data;
            } 
        });
    },
    home: function () {
        this.$router.go(-1);
    },
    logout: function () {
            this.$parent.logout();
    },
  },
  created () {
      this.$parent.checkAuth();
      this.getUsers();
  }

}
</script>

<style>

.usersContent{
    border: 2px solid #778899; 
    background-color: #FAFAD2;
    border-radius: 3px;
}

.user{
    background-color: #90EE90;
    width: 150px;
    margin-top: 10px;
    margin-bottom: 10px;
    height: 40px;
    border: 1px solid #778899; 
    border-radius: 3px;
    text-align: center;
    display: inline-block;
}
.user > p {
    margin-top: 10px;
    display: inline-block;
}

.userAct > span {
    margin-left: 50px; 
}
.userAct > button{
    float: right;
    margin-top: 15px;
    margin-right: 30px;
}
/* .user > button{
    display: inline-block;
} */
#app{
    /* background-color: 	#3CB371; */
}

.noActive{
    color: red;
}

.active {
    color: green;
}

.addUser{
    text-align: center;
    margin-top: 20px;
}
</style>

<template>
<div>
    <div>
        <!-- <div class="navBar"> -->
        <button type="button" class="btn btn-success home"
            v-on:click="home" >Home</button>
        <!-- </div> -->
        <!-- <div class="navBar"> -->
        <button type="button" class="btn btn-danger logout"
            v-on:click="logout" >Logout</button>
        <!-- </div> -->
    </div>
     <div class="row">
         <div class="roomsBoard">
             <div class="roomBoard" v-for="room in rooms">
                 <button class="btn btn-info" 
                 v-on:click="showBoardroom(room.id,$event)">
                     {{room.name}}
                 </button>
             </div>
         </div>
  <router-view class="calendar"
   v-if="room"
   name="calendar"></router-view>
   <div>
       <div class="actionBtn">
           <div>
                <button class="btn"
                v-on:click="bookIt">Book it!</button>
           </div>
           <div v-if="role == 'admin'">
                <button class="btn"
                v-on:click="employeeList">Employee List</button>
           </div>
       </div>
   </div>
  </div>
  </div>
</template>

<script>

export default {
    data() {
        return {
            rooms: ROOMS,
            room: true,
            roomId: '',
            role: ''
        }
    },
    methods: {
        home: function () {
            this.$router.push({ path: '/mainPage/calendar/'+this.roomId});
        },
        logout: function () {
            this.$parent.logout();
        },
        bookIt: function () {
            this.$router.push({ path: '/bookit/'+this.roomId});
        },
        employeeList: function () {
            this.$router.push({ path: '/employee'});
        },
        showBoardroom: function (roomId,event) {
            this.$router.push({ path: '/mainpage/calendar/'+roomId});
        }
    },
    created() {
        this.$parent.checkAuth();
        this.roomId = this.$route.params.id;
        this.role = this.$parent.getCookie('role');
    },
    watch: {
    '$route':function (to, from) {
        this.roomId = to.params.id;
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.roomBoard{
    display: inline-block;
    text-align: center;
    margin-left: 20px;
}
.roomsBoard{
    text-align: center;
}
.roomBoard >button:target  {
  color: #BA55D3
}
.calendar{
    float: left;
}
.actionBtn{
    margin-top: 50px;
    text-align: center;
}
.actionBtn > div{
    margin-top: 20px;
}
</style>

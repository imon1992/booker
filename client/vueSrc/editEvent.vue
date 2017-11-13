<template>
    <div class="eventInfo">
        <div class="header">
            <div>
                B.B. DETAILS
            </div>
        </div>
        <div class="contentBlock">
            <div class="leftColl">
                <div class="lables">
                    <p>
                        When:
                    </p>
                    </div>
                <div class="lables">
                    <p>
                        Notes:
                    </p>
                </div>
                <div class="lables">
                    <p>
                        Who:
                     </p>
                </div>
            </div>
            <div class="rightColl">
                <div class="time">
                    <div>
                        <div v-for="event in eventInfo">
                            <input class="form-control" 
                            v-model="event.startTime"
                            v-bind:value="event.startTime"/>
                        </div>
                        <span>-</span>
                        <div v-for="event in eventInfo">
                            <input class="form-control"
                            v-model="event.endTime"
                             v-bind:value="event.endTime"/>
                        </div>
                    </div>
                    <span class="error">{{errorMsgs.timeErr}}</span>
                </div>
                <div class="desc">
                    <div v-for="event in eventInfo">
                        <input class="form-control" v-model="event.description"
                        v-bind:value="event.description"/>
                    </div>
                    <span class="error">{{errorMsgs.descErr}}</span>
                </div>
                <div class="person">
                <select v-if="role=='user'"
                class="form-control select" v-model="userId">
                        <option v-for="event in eventInfo"
                        v-bind:value="event.id">{{event.name}}</option>
                    </select>
                    <select v-else
                class="form-control select" v-model="userId">
                        <option v-for="user in users"
                        v-if="user.isActive=='active'"
                        v-bind:value="user.id">{{user.name}}</option>
                    </select>
                </div>
            </div>

            </div>
                <div class="dateCreate" v-for="event in eventInfo">
                    <div>
                        Create date : {{event.timeOfCreate}}
                    </div>
                </div>
                <div class="recurrence">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" value="1" 
                        v-model="recurrence"> Apply to all occurrences?
                        </label>
                    </div>
                </div>
                <div class="updateDelete">
                    <button v-on:click="updateEvent"
                     class="btn btn-primary">Update</button>
                    <button v-on:click="deleteEvent" class="btn btn-danger">Delete</button>
                </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            eventInfo: '',
            errorMsgs: {
                timeErr: '',
                descErr: ''
            },
            recurrence: '',
            users: {},
            userId: '',
            role: ''
        };
    },
    methods: {
        updateEvent: function () {
            var self = this;
            this.checkError();
            var recurrence;
            if(this.recurrence == true){
                recurrence = 1;
            } else {
                recurrence = 0;
            }

            if(this.errorMsgs.timeErr == '' && this.errorMsgs.descErr == ''){
                fetch(EVENT_URL, {
                    method: 'put',
                    headers: {  
                        "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"  
                    },
                    credentials: "include",
                    body: 'date='+JSON.stringify(this.$route.params.eventDate)
                    +'&eventId='+JSON.stringify(this.$route.params.eventId)
                    +'&userId='+JSON.stringify(this.userId)
                    +'&desc='+JSON.stringify(this.eventInfo[0].description)
                    +'&recurrence='+JSON.stringify(recurrence)
                    +'&startTime='+JSON.stringify(this.eventInfo[0].startTime)
                    +'&endTime='+JSON.stringify(this.eventInfo[0].endTime)
                    +'&roomId='+JSON.stringify(this.$route.params.bedroomId)
                })
                .then(this.$parent.status)
                .then(this.$parent.json)
                .then(function (data) {
                    if(data == true)
                    {
                        window.opener.c();
                        window.close();
                    }
                    else if(data.busyDates != null) {
                        var noUpdatesgDates = EVENT_NO_ADD_DATES;
                        var lastElement = data.busyDates.length-1
                        for(var key  in data.busyDates){
                            if(lastElement != key){
                                noUpdatesgDates = noUpdatesgDates + data.busyDates[key] + ', '
                            } else {
                                noUpdatesgDates = noUpdatesgDates + data.busyDates[key];
                            }
                        }
                        self.errorMsgs.timeErr = noUpdatesgDates + 'select other';
                    }
                });
            }
        },
        deleteEvent: function () {
            var self = this;
            var recurrence;
            this.checkError();
            if(this.recurrence == true){
                recurrence = 1;
            } else {
                recurrence = 0;
            }
            fetch(EVENT_URL, {
                method: 'delete',
                headers: {  
                    "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"  
                },  
                credentials: "include",
                body: 'date='+JSON.stringify(this.$route.params.eventDate)
                +'&eventId='+JSON.stringify(this.$route.params.eventId)
                +'&recurrence='+JSON.stringify(recurrence)
            })
            .then(this.$parent.status)
            .then(this.$parent.json)
            .then(function (data) {
                if(data == true){
                    window.opener.c();
                    window.close();
                }
            });
        },
        checkError: function () {
            if(this.eventInfo.startTime == '' && this.eventInfo.endTime == ''){
                this.errorMsgs.timeErr = REQUIRED_FIELD;
            } else {
                var checkStartTime = this.eventInfo[0].startTime.match( /^[0-9]{2}:00|30$/i );
                var checkEndTime = this.eventInfo[0].endTime.match( /^[0-9]{2}:00|30$/i );
                if((checkStartTime == null || checkEndTime == null)
                ||(this.eventInfo[0].startTime > this.eventInfo[0].endTime)
                ||(this.eventInfo[0].startTime < '08:00')
                || (this.eventInfo[0].endTime > '20:00')){
                    this.errorMsgs.timeErr = NO_VALID_TIME;
                } else {
                    this.errorMsgs.timeErr ='';
                }
            }

            if(this.eventInfo.description == ''){
                this.errorMsgs.descErr = REQUIRED_FIELD;
            } else {
                this.errorMsgs.descErr = '';
            }
        }

    },
    created() {
        this.$parent.checkAuth();
        var self = this;
        this.role = this.$parent.getCookie('role');
        var paramsLine = this.$route.params.bedroomId + '/' + this.$route.params.userId 
        + '/' + this.$route.params.eventId + '/' + this.$route.params.startTime + ':00'
        fetch(EVENT_URL + paramsLine, {
            method: 'get',
            credentials: "include"
        })
        .then(this.$parent.status)
        .then(this.$parent.json)
        .then(function(data) {
            if (data != false) {
                data[0].startTime = data[0].startTime.substr(0,5);
                data[0].endTime = data[0].endTime.substr(0,5);
                self.eventInfo = data;
                self.userId = data[0].id;
            }
        });
        if(this.role == 'admin'){
            fetch(USER_URL, {
                method: 'get',
                credentials: "include"
            })
            .then(this.$parent.status)
            .then(this.$parent.json)
            .then(function(data) {
                if (data != false) {
                    self.users = data;
                }
            });
        }
    }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.header, .dateCreate {
    width: 280px;
    height: 40px;
    margin: auto;
    background-color: #90EE90;
    border: 1px solid #778899;
    border-radius: 3px;
    margin-top: 20px;
}
.header>div,.dateCreate>div {
    width: 260px;
    margin: auto;
    margin-top: 8px;
    text-align: center;
    border: 1px solid #778899;
    border-radius: 3px;
}
.contentBlock{
    width: 280px;
    height: 210px;
    margin: auto;
}
.leftColl {
    float: left;
}
.rightColl{
    float: right;
}
.lables {
    height: 65px;
    width: 60px;
    background-color: #90EE90;
    border: 1px solid #778899;
    border-radius: 3px;
    text-align: center;
    margin-top: 2px;
}
.lables > p{
    margin-top: 20px;
}
.time,.desc,.person {
    height: 65px;
    width: 210px;
    background-color: #FAFAD2;
    border: 1px solid #778899;
    border-radius: 3px;
    text-align: center;
    margin-top: 2px;
}
.desc > div{
    float: left;
}
.desc input{
    width: 190px
}
.time input{
    width: 65px
}
.time > div > div, .desc > div,.person > select{
    display: inline-block;
    margin-left: 10px;
    margin-right: 10px;
    margin-top: 15px;
}
.person > select {
    margin-top: 15px;
}
.person select{
    width: 190px;
}
.dateCreate{

    margin-top: 0;
    height: 30px;
}
.dateCreate > div {
    margin-top: 3px;
}
.eventInfo {
    background-color: 	#3CB371;
    width: 300px;
    margin: auto;
}
.error{
    color: red;
    width: 15px;
}
.updateDelete{
        text-align: center;
    margin-top: 10px;
}
.updateDelete >button{
    margin-left: 20px;
    margin-right: 20px;
}
.recurrence{
    margin-left: 15px;
}
.checkbox label{
        font-weight: bold;
}
</style>

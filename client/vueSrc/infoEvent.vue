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
                            <span>{{event.startTime}}</span>
                        </div>
                        <span>-</span>
                        <div v-for="event in eventInfo">
                            <span>{{event.endTime}}</span>
                        </div>
                    </div>
                </div>
                <div class="desc">
                    <div v-for="event in eventInfo">
                        <span>{{event.description}}</span>
                    </div>
                </div>
                <div class="person">
                    <div v-for="persone in eventInfo">
                        <span>{{persone.name}}</span>
                    </div>
                </div>
            </div>

            </div>
                <div class="dateCreate" v-for="event in eventInfo">
                    <div>
                        Create date : {{event.timeOfCreate}}
                    </div>
                </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            eventInfo: {},
        };
    },
    created() {
        this.$parent.checkAuth();
        var self = this;
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
                self.eventInfo = data;
            }
        });
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
.time > div > div, .desc > div,.person > div{
    display: inline-block;
    margin-left: 10px;
    margin-right: 10px;
    margin-top: 20px;
    text-align: center;
    font-weight: bold;
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

</style>

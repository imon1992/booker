<template>
    <div>
    <div >
      <button type="button" class="btn btn-success home"
          v-on:click="home" >Home</button>
          <button type="button" class="btn btn-danger logout"
            v-on:click="logout" >Logout</button>
    </div>
        <div>
            <div class="col-md-3"></div>
            <div class="col-md-6 bookIt">
            <div class="bookedFor">
                <label>1. Booked for:</label>
                <div>
                    <select class="form-control select" v-model="bookerFor">
                        <option v-if="user.isActive=='active'"
                        v-for="user in users" v-bind:value="user.id">{{user.name}}</option>
                    </select>
                </div>
            </div>

            <div class="date">
                <label>2. I would like to book this meeting</label>
                <div >
                    <div>
                        <select class="form-control year" v-model="selectedYear" v-on:change="setNewMonth">
                            <option v-for="year in yearsDaysMonth" v-bind:value="year.year">{{year.year}}</option>
                        </select>

                        <select class="form-control month" v-model="selectedMonth">
                            <option v-for="month in yearsDaysMonth[selectedYear].montDay.month" v-bind:value="month.id">{{month.name}}</option>
                        </select>

                        <select class="form-control day" v-model="selectedDay">
                            <option v-for="month in yearsDaysMonth[selectedYear].montDay.days[selectedMonth]" v-bind:value="month">{{month}}</option>
                        </select>
                    </div>
                    <div class="error">{{weekendDates}}</div>
                    <div class="error">{{noAddingDates}}</div>
                </div>
            </div>

            <div class="time">
                <label>3. Specify what the time and end of the meeting(This will be what people see when they click on an eventLink)</label>
                <button  class="btn"
                v-on:click="showAmPm">AM PM/Normal</button>
                <div>
                    <label>from</label>
                    <select class="form-control hours" 
                    v-model="fromTime.hour"
                    v-on:change="changeMinutes">
                        <option v-for="hour in timeHoursFrom" v-bind:value="hour">{{hour}}</option>
                    </select>
                    <select class="form-control min" v-model="fromTime.minutes">
                        <option v-for="min in timeMinutesFrom" v-bind:value="min">{{min}}</option>
                    </select>
                    <select v-if="amPm" class="form-control amPm" 
                    v-model="fromTime.amPm"
                    v-on:change="changeAmPmTime('from')">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                        </option>
                    </select>
                    <div  v-bind:class="fromError">no valid time</div>
                </div>
                <div>
                    <label class="to">to</label>
                    <select class="form-control hours" 
                    v-model="toTime.hour"
                    v-on:change="changeMinutes">
                        <option v-for="hour in timeHoursTo" v-bind:value="hour">{{hour}}</option>
                    </select>
                    <select class="form-control min" v-model="toTime.minutes">
                        <option v-for="min in timeMinutesTo" v-bind:value="min">{{min}}</option>
                    </select>
                    <select v-if="amPm" class="form-control amPm" 
                    v-model="toTime.amPm"
                    v-on:change="changeAmPmTime('to')">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                        </option>
                    </select>
                </div>
                <div v-bind:class="toError">no valid time</div>
            </div>

            <div class="desc">
                <label>4. Enter the specifics for the meeting(This will be what people see when they click on an evenl link)</label>
                <textarea class="form-control description" rows="3" placeholder="Description"
                v-model="desc"></textarea>
                <div class="error">{{descError}}</div>
            </div>

            <div class="recurring">
                <label>5. It this going to be a recurring event?</label>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" value="no" v-model="recurring"> no
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" value="yes" v-model="recurring"> yes
                    </label>
                </div>
            </div>

            <div v-if="recurring == 'yes'" class="recurringInfo">
                <label>6. It iss recurring specify weekly,bi-weekly,or monthly.</label>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" value="weekly" v-model="recurrence"> weekly
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" value="bi-weekly" v-model="recurrence"> bi-weekly
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" value="monthly" v-model="recurrence"> monthly
                    </label>
                </div>

                <label>If weekly or bi-weekly,specify the number of weeks for it to keep recurring. If monthly, specify the number of month. (if yoy choose 'bi-weekly' and put in an add number of weeks, the computer will round down.)
                </label>

                <input v-model="repetitionCount" class="form-control"> duration (max {{maxWeeks()}} repetition)
                <div v-bind:class="repetitionError">invalid repetition count</div>
            </div>
            <button  class="btn btn-success" v-on:click="sendData">Submit</button>
            <div style="clear:both;"></div>
    </div>
<div class="col-md-3"></div>
</div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      users: "",
      allMonth: MONTHS,
      timeHoursFrom: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
      timeHoursTo: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
      timeMinutesFrom: TIME_MINUTES,
      timeMinutesTo: TIME_MINUTES,
      apPmclickCount: 1,
      amPm: false,
      recurring: RECURRING,
      recurrence: DEFAULT_RECURRECE,
      yearsDaysMonth: {},
      yearsCountInSelect: SHOW_YEAR_COUNT,
      selectedYear: "",
      selectedMonth: "",
      selectedDay: 1,
      repetitionCount: "",
      fromTime: {
        hour: DEFAULT_FROM_HOUR,
        minutes: DEFAULT_FROM_MINUTES,
        amPm: ""
      },
      toTime: {
        hour: DEFAULT_TO_HOUR,
        minutes: DEFAULT_TO_MINUTES,
        amPm: ""
      },
      bookerFor: "",
      desc: "",
      fromError: {
        error: false,
        noError: true
      },
      toError: {
        error: false,
        noError: true
      },
      repetitionError: {
        error: false,
        noError: true
      },
      descError: "",
      weekendDates: "",
      noAddingDates: ""
    };
  },
  methods: {
    home: function() {
      this.$router.push({
        path: "/mainPage/calendar/" + this.$route.params.roomId
      });
    },
    logout: function () {
      this.$parent.logout();
    },
    addZeroBeforeTime: function (param,paramLength) {
      if (param.toString().length == paramLength) {
        var param = "0" + param;
      }
      return param;
    },
    sendData: function() {
      var selectedMonth = this.addZeroBeforeTime(this.selectedMonth,1);
      var selectedDay = this.addZeroBeforeTime(this.selectedDay,1);
      var eventDate = this.selectedYear + "-" 
      + (parseInt(selectedMonth, 10) + 1) + "-" + selectedDay;
      if (this.fromTime.amPm == "" && this.toTime.amPm == "") {
        var fromHour = this.addZeroBeforeTime(this.fromTime.hour,1);
        var timeFrom = fromHour + ":" + this.fromTime.minutes + ":00";

        var toHour = this.addZeroBeforeTime(this.toTime.hour,1);
        var timeTo = toHour + ":" + this.toTime.minutes + ":00";
      } else {
        if (this.fromTime.amPm == "PM") {
          if(this.fromTime.hour == 12){
            timeFrom = '12' + ":" + this.fromTime.minutes + ":00";
          } else {
            timeFrom = this.fromTime.hour + 12 + ":" + this.fromTime.minutes + ":00";
          }
        } else {
          timeFrom = this.fromTime.hour + ":" + this.fromTime.minutes + ":00";
        }

        if (this.toTime.amPm == "PM") {
           if(this.toTime.hour == 12){
             timeTo = '12' + ":" + this.fromTime.minutes + ":00";
           } else {
             timeTo = this.toTime.hour + 12 + ":" + this.fromTime.minutes + ":00";
           }
        } else {
          timeTo = this.toTime.hour + ":" + this.toTime.minutes + ":00";
        }
        timeTo = this.addZeroBeforeTime(timeTo,7);
        timeFrom = this.addZeroBeforeTime(timeFrom,7);
      }

      if (this.recurring == "yes") {
        var recurrence = this.recurrence;
      } else {
        recurrence = null;
      }
      this.checkFields(timeFrom,timeTo)
      var date = new Date();
      var month = this.addZeroBeforeTime(date.getMonth(),1);
      var day = this.addZeroBeforeTime(date.getDate(),1);
      var hours = this.addZeroBeforeTime(date.getHours(),1);
      var min = this.addZeroBeforeTime(date.getMinutes(),1);
      var sec = this.addZeroBeforeTime(date.getSeconds(),1);
      var dateCreate = date.getFullYear() +"-" +(parseInt(month, 10) + 1) + "-" 
      + day + " " + hours + ":" + min + ":" + sec;

      if (this.bookerFor != "" && this.$route.params.roomId != "" 
      && this.desc != "" && dateCreate != "" && eventDate != "" 
      && this.toError.error == false) {
          this.addEvents(this.bookerFor, this.$route.params.roomId, this.desc,
            eventDate, dateCreate, recurrence, timeFrom, timeTo, 
            this.repetitionCount, date.getTimezoneOffset());
      }
    },
    checkFields: function(timeFrom,timeTo) {
      if (this.desc == "") {
        this.descError = REQUIRED_FIELD;
      } else {
        this.descError = "";
      }

      if (this.repetitionCount > this.maxWeeks() || this.repetitionCount == "" 
      && this.recurring == "yes") {
        this.repetitionError.error = true;
        this.repetitionError.noError = false;
        this.repetitionCount = "";
      } else {
        this.repetitionError.error = false;
        this.repetitionError.noError = true;
      }

      if (timeFrom == timeTo || timeFrom > timeTo) {
        this.toError.error = true;
        this.toError.noError = false;
      } else {
        this.toError.error = false;
        this.toError.noError = true;
      }

    },
    addEvents: function(userId, roomId, desc, eventDate, dateCreate,
      recurrence, timeFrom, timeTo, repetitionCount, timeZone) {
      var self = this;
      fetch(EVENT_URL, {
        method: "post",
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        credentials: "include",
        body: "userId=" + JSON.stringify(userId) + "&roomId=" + JSON.stringify(roomId) 
        + "&description=" + JSON.stringify(desc) + "&date=" + JSON.stringify(eventDate)
        + "&timeOfCreate=" + JSON.stringify(dateCreate) + "&recursive=" + JSON.stringify(recurrence) 
        + "&timeStart=" + JSON.stringify(timeFrom) + "&timeEnd=" + JSON.stringify(timeTo) 
        + "&repetitionCount=" + JSON.stringify(repetitionCount) + "&timeZone=" + JSON.stringify(timeZone)
      })
        .then(this.$parent.status)
        .then(this.$parent.json)
        .then(function(data) {
          if (data == true) {
            alert(EVENT_SUCCESS_ADD);
            self.descError = '';
            self.fromError.error = false;
            self.toError.error = false;
            self.repetitionError.error = false;
            self.weekendDates = '';
            self.noAddingDates = '';
          } else if (data == false) {
            alert(EVENT_ADD_ERR);
          } else if (data.busyDates != null) {
            var noAddingDates = EVENT_NO_ADD_DATES;
            var lastElement = data.busyDates.length - 1;
            for (var key in data.busyDates) {
              if (lastElement != key) {
                noAddingDates = noAddingDates + data.busyDates[key] + ", ";
              } else {
                noAddingDates = noAddingDates + data.busyDates[key];
              }
            }
            self.noAddingDates = noAddingDates;
            alert(noAddingDates + "select other");
          } else if (data.weekendDays != null) {
            var weekendDates = EVENT_WEEKEND_DATES;
            var lastElement = data.weekendDays.length - 1;
            for (var key in data.weekendDays) {
              if (lastElement != key) {
                weekendDates = weekendDates + data.weekendDays[key] + ", ";
              } else {
                weekendDates = weekendDates + data.weekendDays[key];
              }
            }
            self.weekendDates = weekendDates;
            alert(weekendDates + "select other");
          }
        });
    },
    timeError: function() {
    },
    maxWeeks: function() {
      return RECURRECE_VALUES[this.recurrence];
    },
    setNewMonth: function() {
      var date = new Date();
      if (this.selectedYear != date.getFullYear()) {
        this.selectedMonth = 0;
      } else {
        this.selectedMonth = date.getMonth();
      }
    },
    changeFromToAmPm: function (amPm,fromToTime) {
      if(amPm == 'PM')
      {
        this[fromToTime] = [12,1,2,3,4,5,6,7,8];
      }

      if(amPm == 'AM')
      {
        this[fromToTime] = [8, 9, 10, 11];
      }
    },
    changeAmPmTime: function (fromTo)  {
      if(fromTo == 'from')      {
        this.changeFromToAmPm(this.fromTime.amPm,'timeHoursFrom')
      }

      if(fromTo == 'to')
      {
        this.changeFromToAmPm(this.toTime.amPm,'timeHoursTo')
      }
      this.changeMinutes();
    },
    changeMinutes: function () {
      if(this.fromTime.hour == 20 || (this.fromTime.hour == 8 && this.fromTime.amPm == 'PM')) {
        this.timeMinutesFrom = ['00'];
      }  else {
       this.timeMinutesFrom =  TIME_MINUTES;
      }

      if(this.toTime.hour == 20 || (this.toTime.hour == 8 && this.toTime.amPm == 'PM')) {
        this.timeMinutesTo = ['00'];
      }  else {
        this.timeMinutesTo =  TIME_MINUTES;
      }
    },
    setAmPmNormalParams: function (timeHours,amPmStatus) {
        this.timeHoursFrom = timeHours;
        this.timeHoursTo = timeHours;
        this.fromTime.hour = 8;
        this.toTime.hour = 8;
        this.amPm = amPmStatus;
        this.fromTime.amPm = "AM";
        this.toTime.amPm = "AM";
        this.apPmclickCount++;
        this.timeMinutesTo =  TIME_MINUTES;
        this.timeMinutesFrom =  TIME_MINUTES;
    },
    showAmPm: function() {
      if (this.apPmclickCount % 2 == 1) {
        this.setAmPmNormalParams([8, 9, 10, 11],true)
      } else {
        this.setAmPmNormalParams([8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],false)
      }
    },
    getUsers: function (url){
      var self = this;
      fetch(url, {
        method: "get",
        credentials: "include"
      })
        .then(this.$parent.status)
        .then(this.$parent.json)
        .then(function(data) {
          if (data != false) {
            for(var key in data){
              if(data[key].isActive == 'active'){
                self.bookerFor = data[key].id;
                break;
              }
            }
            self.users = data;
          }
        });
    },
    generateYearsMontDays: function () {
      var date = new Date();
      var nowDate = new Date();
      this.selectedMonth = date.getMonth();
      this.selectedYear = date.getFullYear();

      var yearsDaysMonth = {};
      for (var i = 0; i < this.yearsCountInSelect; i++) {
        var year = date.getFullYear();
        var years = { year: year, montDay: { month: [], days: [] } };

        var countDayInMonth = new Date(date.getFullYear(),date.getMonth() + 1, 0).getDate();
        for (var a = date.getMonth(); a < 12; a++) {
          years["montDay"]["month"].push({ id: a, name: this.allMonth[a] });
          years["montDay"]["days"][a] = [];

          countDayInMonth = new Date(date.getFullYear(),date.getMonth() + 1,0).getDate();
          for (var j = 0; j < countDayInMonth; j++) {
            if(nowDate.getDate() > date.getDate() && date.getMonth() >= nowDate.getMonth())
            {
            } else {
                years["montDay"]["days"][a].push(date.getDate());
            }

            date.setDate(date.getDate() + 1);
          }
        }
        yearsDaysMonth[year] = years;
      }
      this.selectedDay = nowDate.getDate();
      this.yearsDaysMonth = yearsDaysMonth;
      }
  },
  created() {
    this.$parent.checkAuth();
    this.generateYearsMontDays();
    if (this.$parent.getCookie("role") == "admin") {
      this.getUsers(USER_URL)
    } else {
      this.getUsers(USER_URL + this.$parent.getCookie("id"))
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.select {
  width: 250px;
}

.hours, .min, .amPm {
  width: 70px;
  display: inline-block;
}
.year {
  width: 80px;
  display: inline-block;
}
.day {
  width: 70px;
  display: inline-block;
}
.month {
  width: 120px;
  display: inline-block;
}
.to {
  margin-right: 18px;
}
.description {
  width: 250px;
}
.error {
  color: red;
  border-color: red;
}
.noError {
  display: none;
}
.bookedFor > div {
  margin-left: 180px;
}
.date > div {
  margin-left: 165px;
}
.time > button {
  margin-left: 250px;
}
.time > div {
  margin-left: 200px;
}
.desc > textarea {
  margin-left: 190px;
}
.desc > div {
  margin-left: 250px;
}
.recurring > div {
  margin-left: 250px;
}
.recurringInfo > div {
  margin-left: 250px;
}
.recurringInfo > input {
  margin-left: 250px;
  margin-bottom: 15px;
  display: inline-block;
  width: 200px;
}
.bookIt {
  border: 1px solid gray;
  border-radius: 5px;
  background-color: #fafad2;
}
.bookIt > button {
  margin-left: 95px;
  width: 400px;
}
</style>

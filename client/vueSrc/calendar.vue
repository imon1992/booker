<template>
<div>
  <div class="calendar">
      <div class="clendarAction">
        <div>
          <button class="btn btn-primary"
          v-on:click="prevMonth(monthId)">prew</button>
          {{monthName}} {{year}}
          <button class="btn btn-primary"
          v-on:click="nextMonth(monthId)">next</button>
        </div>
        <div class="amPmAction">
          <button class="btn btn-primary"
           v-on:click="changeToAmPm"> AM Pm/Normal</button>
        </div>
      </div>
    <div class="dateTable">
      <div class="dayNames">
        <div class="dateCell" v-for="day in daysName">
          <span>{{day}}</span>
        </div>
      </div>
      <div class="dateRow" v-for="week in weekDays">
        <div class="cellEvent" v-for="day in week">
          <span>{{day.day}}</span>
          <div v-for="event in day.events">
            <!-- <div </div> -->
            <a v-on:click="newWindow(event[0].startTime,event[0].userId,event[0].eventId,day.day)">{{event[0].startTime}}-{{event[0].endTime}}</a>
          </div>
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
      daysName: "",
      days: {},
      weekDays: "",
      month: {},
      montId: "",
      monthName: "",
      allMonth: MONTHS,
      year: "",
      eventsData: "",
      deleteId: "",
      amPmNormalState: false
    };
  },
  methods: {
    getDay: function(date) {
      var day = date.getDay();
      if (day == 0) day = 7;
      return day - 1;
    },
    changeToAmPm: function() {
      if (this.amPmNormalState == false) {
        this.toAmPm();
      } else {
        this.toNormal();
        this.getDataAndShowCalendar();
      }
      this.generateCalendar(this.year, this.monthId);
    },
    addZeroBeforeTime: function (param,paramLength) {
      if (param.toString().length == paramLength) {
        var param = "0" + param;
      }
      return param;
    },
    toNormal: function() {
      this.daysName = EUROPE_WEEK_DAYS;
      this.amPmNormalState = false;
    },
    toAmPm: function() {
      for (var key in this.eventsData) {
        var timeEnd = this.eventsData[key].events[0].endTime.substr(0, 2);
        var timeStart = this.eventsData[key].events[0].startTime.substr(0, 2);
        timeEnd = timeEnd * 1;
        timeStart = timeStart * 1;

        if (timeEnd < 12) {
          timeEnd = this.eventsData[key].events[0].endTime + "AM";
        } else {
          timeEnd = timeEnd - 12;
          if (timeEnd == 0) {
            timeEnd = "12" + this.eventsData[key].events[0].endTime.substr(-3) + "AM";
          } else {
            timeEnd = this.addZeroBeforeTime(timeEnd) 
            + this.eventsData[key].events[0].endTime.substr(-3) + "PM";
          }
        }

        if (timeStart < 12) {
          timeStart = this.eventsData[key].events[0].startTime + "AM";
        } else {
          timeStart -= 12;
          if (timeStart == 0) {
            timeStart =
              "12" + this.eventsData[key].events[0].startTime.substr(-3) + "PM";
          } else {
            if (timeStart.toString().length == 1) {
              timeStart = "0" + timeStart;
            }
            timeStart =
              timeStart +
              this.eventsData[key].events[0].startTime.substr(-3) +
              "PM";
          }
        }
        this.eventsData[key].events[0].endTime = timeEnd;
        this.eventsData[key].events[0].startTime = timeStart;
      }
      this.daysName = AMERICAN_WEEK_DAYS;
      this.amPmNormalState = true;
    },
    generateCalendar: function(year, month) {
      var days = [];
      var date = new Date(year, month);

      for (var i = 0; i < this.getDay(date); i++) {
        days.push({ day: "" });
      }
      while (date.getMonth() == month) {
        days.push({ day: date.getDate() });

        date.setDate(date.getDate() + 1);
      }
      if (this.daysName[0] == "Sunday") {
        days.unshift("");
      }

      var dayslength = days.length;
      var weekDays = [];
      this.days = days;
      while (days.length != 0) {
        weekDays.push(days.splice(0, 7));
      }
      var c = 0;
      for (var key in weekDays) {
        for (var day in weekDays[key]) {
          if (weekDays[key][day] == "" || weekDays[key][day].day == "") {
            c++;
          } else {
            var events = {};
            var i = 0;
            for (var keyEvent in this.eventsData) {
              var eventDay = this.eventsData[keyEvent].date.substr(-2);
              if (eventDay == weekDays[key][day].day) {
                this.eventsData[keyEvent].events[0]["eventId"] = this.eventsData[keyEvent].id;
                this.eventsData[keyEvent].events[0]["userId"] = this.eventsData[
                  keyEvent
                ].userId;
                events[i] = this.eventsData[keyEvent].events;
                weekDays[key][day] = {
                  day: weekDays[key][day].day,
                  events: events
                };
                i++;
              }
            }
          }
        }
        if (c == 7) {
          weekDays.splice(key, 1);
          c = 0;
        }
      }

      var lengthLastWeek = weekDays[weekDays.length - 1].length;
      if (lengthLastWeek < 7) {
        var needAdd = 7 - lengthLastWeek;
        for (var i = 0; i < needAdd; i++) {
          weekDays[weekDays.length - 1].push("");
        }
      }

      this.weekDays = weekDays;
      this.monthName = this.allMonth[this.monthId];
    },
    nextMonth(monthId) {
      if (monthId == 11) {
        this.year = this.year + 1;
        this.monthId = 0;
      } else {
        this.monthId = monthId + 1;
      }
      this.getDataAndShowCalendar();
    },
    prevMonth(monthId) {
      if (monthId == 0) {
        this.year = this.year - 1;
        this.monthId = 11;
      } else {
        this.monthId = monthId - 1;
      }
      this.getDataAndShowCalendar();
    },
    fromTo: function() {
      var date = new Date();
      var lastMontDay = new Date(this.year, this.monthId + 1, 0);
      var monthId = this.monthId + 1;
      var from = this.year + "-" + monthId + "-01";
      var to = this.year + "-" + monthId + "-" + lastMontDay.getDate();

      return { from: from, to: to };
    },
    getDataAndShowCalendar: function() {
      var self = this;
      var ftomTo = this.fromTo();
      var id = this.$route.params.id;
      fetch(EVENT_URL + ftomTo.from + "/" + ftomTo.to + "/" + id, {
        method: "get",
        credentials: "include",
      })
        .then(this.$parent.$parent.status)
        .then(this.$parent.$parent.json)
        .then(function(data) {
          if (data != false) {
            self.eventsData = data;
            self.generateCalendar(self.year, self.monthId);
          } else {
            self.eventsData = {};
            self.generateCalendar(self.year, self.monthId);
          }
        });
    },
    newWindow: function(startTime, userEventId, eventId, day) {
      if (this.amPmNormalState == true) {
        var amPm = startTime.substr(-2);
        var min = startTime.substr(3, 2);
        var hour = startTime.substr(0, 2);
        hour = hour * 1;
        if (amPm == "PM") {
          hour += 12;
        }
        var startTime = hour + ":" + min;
      }
      var bedroomId = this.$route.params.id;

      for (var key in this.allMonth) {
        if (this.allMonth[key] == this.monthName) {
          var month = key * 1 + 1;
        }
      }
      day = this.addZeroBeforeTime(day,1);
      month = this.addZeroBeforeTime(month,1);

      var eventDateTime = new Date(this.year, month - 1, day).valueOf();
      var nowDateTime = new Date().valueOf();

      var date = this.year + "-" + month + "-" + day;
      var paramsLine = bedroomId + "/" + userEventId + "/" + eventId +
        "/" + startTime + "/" + date;
      if (
        (this.$parent.$parent.getCookie("role") == "admin" ||
          userEventId == this.$parent.$parent.getCookie("id")) &&
        eventDateTime > nowDateTime) {
        var newWin = window.open(
          HOST_URL + "editEvent/" + paramsLine,
          HOST_URL + "editEvent/" + paramsLine,
          "width=325,height=380,toolbar=yes,location=no"
        );
      } else {
        var newWin = window.open(
          HOST_URL + "infoEvent/" + paramsLine,
          HOST_URL + "infoEvent/" + paramsLine,
          "width=325,height=325,toolbar=yes,location=no"
        );
      }
    },
    reRender: function() {
      this.toNormal();
      this.getDataAndShowCalendar();
    }
  },
  created() {
    this.$parent.$parent.checkAuth();
    var date = new Date();
    var self = this;
    this.year = date.getFullYear();
    this.monthId = date.getMonth();
    this.getDataAndShowCalendar();
    window.c = function(deleteId) {
      self.reRender(deleteId);
    };
    this.daysName = EUROPE_WEEK_DAYS;
  },
  watch: {
    $route: function(to, from) {
      this.getDataAndShowCalendar();
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.dateTable {
  display: table;
  border: 1px solid gray;
}
.dateRow {
  display: table-row;
}
.dayNames {
  display: table-row;
  text-align: center;
}
.dateCell {
  display: table-cell;
  border: 1px solid gray;
  background-color: #00fa9a;
}
.dateCell > span {
  font-weight: bold;
  font-size: 18px;
}
.cellEvent {
  width: 140px;
  height: 105px;
  display: table-cell;
  border: 1px solid gray;
  background-color: #fafad2;
}
.cellEvent > div {
  text-align: center;
}
.cellEvent > span {
  font-weight: bold;
  font-size: 16px;
}
.calendar {
  margin-left: 20px;
  margin-top: 25px;
}
.clendarAction {
  margin-bottom: 20px;
}
.clendarAction > div {
  display: inline-block;
}
.amPmAction {
  float: right;
}
</style>

//way
const EVENT_URL = 'http://booker/user14/booker/client/api/event/';
// const EVENT_URL = 'http://192.168.0.15/~user14/booker/client/api/event/';
const AUTH_URL = 'http://booker/user14/booker/client/api/auth/';
// const AUTH_URL = 'http://192.168.0.15/~user14/booker/client/api/auth/';
const USER_URL = 'http://booker/user14/booker/client/api/user/';
// const USER_URL = 'http://192.168.0.15/~user14/booker/client/api/user/';
const HOST_URL = 'http://booker/user14/booker/client/dist/#/';
// const HOST_URL = 'http://192.168.0.15/~user14/booker/asd/#/';


const SHOW_YEAR_COUNT = 5;
const TIME_MINUTES = ["00", "30"];
const DEFAULT_RECURRECE = 'weekly';
const RECURRING = 'no';
const RECURRECE_VALUES = { "weekly": 4, "bi-weekly": 2, "monthly": 1 };
const DEFAULT_FROM_HOUR = '8';
const DEFAULT_TO_HOUR = '8';
const DEFAULT_FROM_MINUTES = '00';
const DEFAULT_TO_MINUTES = '00';

//error
const REQUIRED_FIELD = 'This field is required';
const WRONG_EMAIL = 'Wrong Email';
const USER_ADD_ERR = "Error adding";
const USER_EDIT_ERR = 'Edited error';
const USER_DELETE_ERR = 'Error deleted';
const EVENT_ADD_ERR = "Error adding";
const NO_VALID_TIME = 'No valid time';
const EVENT_NO_ADD_DATES = "The date for the selected time is busy: ";
const EVENT_WEEKEND_DATES = "These days fall on the weekend: ";
const LOGIN_TAKEN = 'Login is already taken';

//successful
const EVENT_SUCCESS_ADD = 'Event(s) successfully added';
const USER_SUCCESS_ADD = 'User successfully added';
const USER_SUCCESS_EDIT = 'User successfully edited';
const USER_SUCCESS_DELETED = 'User successfully deleted';

//
const EUROPE_WEEK_DAYS = [
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
  "Sunday"
];
const AMERICAN_WEEK_DAYS = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday"
];
const MONTHS = {
  0: "January",
  1: "February",
  2: "March",
  3: "April",
  4: "May",
  5: "June",
  6: "July",
  7: "August",
  8: "September",
  9: "October",
  10: "November",
  11: "December"
};

const ROOMS = [{ 'id': 1, 'name': 'Boardroom 1' },
{ 'id': 2, 'name': 'Boardroom 2' },
{ 'id': 3, 'name': 'Boardroom 3' }];

var loginTaken = 'login is already taken';
// var eventUrl= 'http://192.168.0.15/~user14/booker/client/api/event/';
var eventUrl = 'http://booker/user14/booker/client/api/event/';
// var authUrl= 'http://192.168.0.15/~user14/booker/client/api/auth/';
var authUrl = 'http://booker/user14/booker/client/api/auth/';
// var userUrl= 'http://192.168.0.15/~user14/booker/client/api/user/';
var userUrl = 'http://booker/user14/booker/client/api/user/';
var host = 'http://booker/user14/booker/client/dist/#/'
      // var host = 'hhttp://192.168.0.15/~user14/booker/asd/#/'
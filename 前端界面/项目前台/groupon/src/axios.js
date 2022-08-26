import axios from "axios";

axios.interceptors.request.use(function(config) {
    if (localStorage.getItem('token')) {
        config.headers.Authorization = localStorage.getItem('token');
    }
    return config;
},function(error) {
    console.log(error);
})

export default axios
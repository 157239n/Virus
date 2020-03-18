<style>
    .sidenav {
        height: 100%;
        width: 30%;
        position: fixed;
        z-index: 1;
        top: 38px;
        left: 0;
        background-color: #111;
        overflow-x: hidden;
        transition: 0.5s;
        padding-top: 60px;
    }

    .sidenav a {
        padding: 8px 8px 8px 32px;
        text-decoration: none;
        font-size: 17px;
        color: #818181;
        display: block;
        transition: 0.3s;
    }

    .sidenav a:hover {
        color: #f1f1f1;
    }

    .sidenav .closeBtn {
        position: absolute;
        top: 0;
        right: 25px;
        font-size: 36px;
        margin-left: 50px;
    }

    .navActive {
        background-color: #333333;
    }

    @media screen and (max-height: 450px) {
        .sidenav {padding-top: 15px;}
        .sidenav a {font-size: 18px;}
    }

    .sidenavClosed {
        left: -30%;
    }

    @media screen and (max-width: 1200px) {
        .sidenav {
            width: 35%;
        }

        .sidenavClosed {
            left: -35%;
        }
    }

    @media screen and (max-width: 950px) {
        .sidenav {
            width: 50%;
        }

        .sidenavClosed {
            left: -50%;
        }
    }

    @media screen and (max-width: 501px) {
        .sidenav {
            width: 100%;
        }

        .sidenavClosed {
            left: -100%;
        }
    }
</style>
angular.module('deleteApp', [])
    .controller('DeleteController', ['$scope', function($scope) {
        $scope.No = function () {
            angular.element(document.querySelector("#deleteAlbum")).css('display', 'none');
        };
        $scope.Form = function () {
            angular.element(document.querySelector("#deleteAlbum")).css('display', 'block').css('z-index', '10');
        };

        $scope.Show = function () {
            angular.element(document.querySelector("#newTrack")).css('display', 'block');
        }

    }]);

angular.module('formApp', [])
    .controller('FormController', ['$scope', '$http', function($scope, $http) {
        var count = angular.element(document.querySelectorAll(".border")).length;
        $scope.cloneDiv = function () {
            count++;
            var borderAppend = angular.element(document.querySelector(".border"));
            var wrapper = angular.element(document.querySelector("#wrapper"));
            borderAppend = "<div class='border'>" + borderAppend.html() +"</div>";
            wrapper.append(borderAppend);

            var border = angular.element(document.querySelectorAll(".border")[count - 1]);
            border.children('.number').text('Track ' + count);
            border.children().children('.file').attr('name', 'track' + count);
            border.children().children().children().children('.track-name').attr('name', 'track_name' + count);
            border.children().children().children().children('.track-name').val('');
            border.children().children().children().children('.track-performer').attr('name', 'track_performer' + count);
            border.children().children().children().children('.track-performer').val('');

            $http({
                method : "POST",
                url : "/tracksCount",
                data: {'count': count}
            });
        };

    }]);

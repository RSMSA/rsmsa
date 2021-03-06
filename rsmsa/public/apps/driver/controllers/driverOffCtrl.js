/**
 * Created by kelvin on 2/13/15.
 */

angular.module('offenceApp').controller('driverOffCtrl',function($scope,$http,$routeParams) {
    //Initialize offence list
    $scope.offenceList = {};
    if($routeParams.id){//If there is an id on the route

        //Fetch
        $http.get("/api/offence/registry/"+$routeParams.id+"/offences").success(function(data) {
            $scope.offenceList = data;
        });
    }else
    {
        $http.get("/api/offences").success(function(data) {
            $scope.offenceList = data;
        });
    }

    /**
     * Delete offence
     *
     * @param Object(Offence) offence
     */
    $scope.deleteOffence = function(offence){
        if(confirm("Are you sure you want to delete this offence"))//If confrims deletion
        {
            //Delete offence from the server
            $http.get("/api/offence/"+offence.id+"/delete/").success(function(data){
                //Remove the deleted offence from the offenceList Model.
                for(i = 0;i < $scope.offenceList.length;i++)
                {
                    if($scope.offenceList[0].id = offence.id)//If offence is in the list
                    {
                        //Delete offence
                        $scope.offenceList.splice(i,1);
                        break;
                    }
                }

            }).error(function(error) {
                //TODO Handle error
            });
        }

    };
});

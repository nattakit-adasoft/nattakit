def githubRepo = 'https://github.com/nattakit-adasoft/nattakit.git'
def githubBranch = 'main'

def dockerRepo = 'naleruto/ada-webserver'

pipeline
{
    agent any
    environment
    {
        imagename = "naleruto/ada-webserver"
        registryCredential = 'naleruto-dockerhub'
        dockerImage = ''
    }
    stages
    {


        stage('Git Clone')
        {
            steps
            {
                echo 'Git Clone'
                git url: githubRepo,
                    branch: githubBranch
            }
        }

        stage('Build')
        {
            steps
            {
                echo 'Building...'
                script
                {
                        dockerImage = docker.build imagename
                }
            }
        }



        stage('Run Container')
        {
            steps
            {
                echo 'Run Container...'
                script
                {
                        bat 'docker run -d --name nattakit-web -p 8889:80 naleruto/ada-webserver'
                }
            }
        }



    //     stage('Test Container')
    //     {
    //         steps
    //         {
    //             echo 'Test Container...'
              
    //         }
    //     }


    //    stage('Stop Container')
    //     {
    //         steps
    //         {
    //             echo 'Stop Container...'
    //             script
    //             {
    //                     bat 'docker stop nattakit-web'

    //             }
    //         }
    //     }


    //    stage('Remove Container')
    //     {
    //         steps
    //         {
    //             echo 'Remove Container...'
    //             script
    //             {
    //                     bat 'docker rm nattakit-web'

    //             }
    //         }
    //     }


        // stage('Deploy Image') {
        //     steps{
        //         script {
        //                 docker.withRegistry( '', registryCredential ) {
        //                     dockerImage.push("$BUILD_NUMBER")
        //                     dockerImage.push('latest')

        //             }
        //         }
        //     }
        // }

    }
}

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
                        bat 'docker run -d \
                        --env BASE_TITLE=AdaSiamKubota \
                        --env BASE_URL=http://sit.ada-soft.com:8889/ \
                        --env BASE_DATABASE=SKC_Fullloop2 \
                        --env DATABASE_IP=147.50.143.126,33433 \
                        --env DATABASE_USERNAME=sa \
                        --env DATABASE_PASSWORD=GvFhk@61 \
                        --env SYS_BCH_CODE=00001 \
                        --env HOST=147.50.143.126 \
                        --env USER=Admin \
                        --env PASS=Admin \
                        --env VHOST=AdaPos5.0SKCDev_Doc \
                        --env EXCHANGE= \
                        --env PORT=5672 \
                        --env MQ_BOOKINGLK_HOST=147.50.143.126 \
                        --env MQ_BOOKINGLK_USER=Admin \
                        --env MQ_BOOKINGLK_PASS=Admin \
                        --env MQ_BOOKINGLK_VHOST=AdaPos5.0SKCDev_Doc \
                        --env MQ_BOOKINGLK_EXCHANGE= \
                        --env MQ_BOOKINGLK_PORT=5672 \
                        --env INTERFACE_HOST=147.50.143.126 \
                        --env INTERFACE_USER=Admin \
                        --env INTERFACE_PASS=Admin \
                        --env INTERFACE_VHOST=AdaPos5.0SKCDev_Doc \
                        --env INTERFACE_EXCHANGE= \
                        --env INTERFACE_PORT=5672 \
                        --env MemberV5_HOST=147.50.143.126 \
                        --env MemberV5_USER=Admin \
                        --env MemberV5_PASS=Admin \
                        --env MemberV5_VHOST=AdaPos5.0SKCDev_Doc \
                        --env MemberV5_EXCHANGE= \
                        --env MemberV5_PORT=5672 \
                        --env MQ_REPORT_HOST=147.50.143.126 \
                        --env MQ_REPORT_USER=Admin \
                        --env MQ_REPORT_PASS=Admin \
                        --env MQ_REPORT_VHOST=AdaPos5.0SKCDev_Doc \
                        --env MQ_REPORT_EXCHANGE= \
                        --env MQ_REPORT_PORT=5672 \
                        --env MQ_Sale_HOST=147.50.143.126 \
                        --env MQ_Sale_USER=Admin \
                        --env MQ_Sale_PASS=Admin \
                        --env MQ_Sale_VHOST=AdaPos5.0SKCDev_Sale \
                        --env MQ_Sale_QUEUES=UPLOADSALE,UPLOADSALEPAY,UPLOADSALERT,UPLOADSALEVD,UPLOADSHIFT,UPLOADTAX,UPLOADVOID \
                        --env MQ_Sale_EXCHANGE= \
                        --env MQ_Sale_PORT=5672 \
                        --name backoffice-web -p 8889:80 naleruto/ada-webserver'
                }
            }
        }



        stage('Test Container')
        {
            steps
            {
                echo 'Test Container...'
                script
                {
                    bat 'cd "C:\ProgramData\Jenkins\.jenkins\workspace\QA automation\"'
                    bat 'robot skc-cr.robot'
                }
            }
        }


       stage('Stop Container')
        {
            steps
            {
                echo 'Stop Container...'
                script
                {
                        bat 'docker stop backoffice-web'

                }
            }
        }


       stage('Remove Container')
        {
            steps
            {
                echo 'Remove Container...'
                script
                {
                        bat 'docker rm backoffice-web'

                }
            }
        }


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

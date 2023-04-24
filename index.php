<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <title>THE SIMELECTION! 2023</title>
</head>

<body>
    <div id="app" class="px-5">

        <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">

                <a class="navbar-brand logo-mono-font fs-4" href="#">THE SIMELECTION! <br> 2023</a>
                <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button> -->

                <!-- <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav ui-mono-font">
                        <li class="nav-item px-5">
                            <button @click="toggleVotePage" class="nav-link" aria-current="page" href="#">Vote</button>
                        </li>
                        <li class="nav-item px-5">
                            <button @click="togglePartyPage" class="nav-link" href="#">Party</button>
                        </li>
                        <li class="nav-item px-5">
                            <button class="nav-link" href="#">About</button>
                        </li>
                    </ul>
                </div> -->
                <button type="button" class="btn btn-warning logo-mono-font">Create own political party</button>
            </div>
        </nav>

        <!-- VOTE -->
        <div class="container position-relative vote-chart">
            <canvas id="myChart" class="position-absolute"></canvas>
                <div class="position-absolute top-50 start-50 translate-middle">
                    <div class="d-flex align-items-center flex-column mb-3">
                        <h6 class="ui-mono-font text-light">Vote!</h6>
                        <button @click="partyPopularity++" type="button" class="btn btn-warning logo-mono-font vote-btn-chart">
                            <span class="material-symbols-outlined fs-1">how_to_vote</span>
                        </button>
                    </div>
                </div>
        </div>
        <!-- VOTE -->
        <!-- FORM -->
        <div class="my-5">
            <div data-bs-theme="dark" class="row ui-mono-font">
                <div class="form-group col">
                    <input type="text" class="form-control" v-model="search" v-on:keyup.enter="getParty" placeholder="Search">
                </div>
                <div class="form-group col col-md-3">
                    <select class="form-select" v-model="sort" @change="getParty">
                        <option value="pname ASC">Sort by Name</option>
                        <option value="scores DESC">Sort by Popularity</option>
                        <option value="id ASC">Sort by Established</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- FORM -->
        <!-- LIST -->
        <div v-if="party.length > 0" class="row mt-4">
            <div v-for="(party,index) in party" :key="index" class="col-12 col-md-4 col-lg-3 mb-4 position-relative">
                <div :class="{ skeleton:loading }" class="card">
                <div class="card-img-top ratio ratio-1x1" :style="{ 'backgroundImage':'url(uploads/'+party.avatar+')' }"></div>
                    <div class="position-absolute top-0 end-0 m-2">
                        <button type="button" @click="onDeleteParty(party.id)" class="btn btn-danger"><span class="material-symbols-outlined d-flex py-1">delete</span></button>
                        <button type="button" @click="onToggleModalEdit(party.id)" data-bs-toggle="modal" data-bs-target="#editModal" class="btn btn-secondary"><span class="material-symbols-outlined d-flex py-1">edit</span></button>
                    </div>
                    <div class="card-body ui-thai-font text-center">
                        <h2>{{ party.pname }}</h2>
                        <h5>Candidate: <br> {{ party.candidate }}</h5>
                        <br>
                        <h5>นโยบาย: <br> {{ party.policy }}</h5>
                        <br>
                        <h6>คะแนนความนิยม: {{ party.scores }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 mb-4">
                <div @click="onClear" data-bs-toggle="modal" data-bs-target="#createModal" type="button" class="card text-bg-warning logo-mono-font on-hover" style="height: 596.594px; transition: 0.3s;" >
                    <div class="text-center position-absolute top-50 start-50 translate-middle">
                        <span class="material-symbols-outlined" style="font-size: 5rem;">add_circle</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIST -->
        <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Create a new political</h5>
                <button @click="onClear" data-bs-toggle="modal" data-bs-target="#createModal" type="button" class="btn-close"></button>
                </div>
                <div class="modal-body">
                <div class="form-group">
                    <div class="mb-2">
                        <img v-if="imagePreview" :src="imagePreview" class="img-fluid mb-2">
                        <input class="form-control" @change="handdelFileUpload" ref="input_file" type="file" accept="image/*">
                    </div>
                    <div class="mb-2">
                        <label>What's your party Name</label>
                        <input type="text" class="form-control" v-model="newParty.pname" placeholder="move on">
                    </div>
                    <div class="mb-2">
                        <label>What's your candidate</label>
                        <input type="text" class="form-control" v-model="newParty.candidate" placeholder="Ms.prayut">
                    </div>
                    <div class="mb-2">
                        <label>How about your policy</label>
                        <input type="text" class="form-control" v-model="newParty.policy" placeholder="We want to plant more trees.">
                    </div>          
                    <div class="mb-2">
                        <label>How your pre-scores</label>
                        <input type="text" class="form-control" v-model="newParty.scores" placeholder="350">
                    </div>        
                </div>
                </div>
                <div class="modal-footer">
                <button data-bs-toggle="modal" data-bs-target="#createModal" @click="onClear" type="button" class="btn btn-secondary">Close</button>
                <button @click="onCreateParty" :disabled="sending" type="button" class="btn btn-primary">
                    <span v-if="sending"> waiting...</span>
                    <span v-else>Save changes</span>
                </button>
                </div>
            </div>
        </div>
    <!-- end modal create -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Edit political</h5>
                    <button @click="onToggleModalEdit('')" data-bs-toggle="modal" data-bs-target="#editModal" type="button" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                    <div class="form-group">
                        <div class="mb-2">
                            <img v-if="imagePreview" :src="imagePreview" class="img-fluid mb-2">
                            <img v-else :src="'uploads/'+editParty.avatar" class="img-fluid mb-2">
                            <input class="form-control" @change="handdelFileUpload" ref="input_file" type="file" accept="image/*">
                        </div>
                        <div class="mb-2">
                            <label>What's your new party Name</label>
                            <input type="text" class="form-control" v-model="editParty.pname">
                        </div>
                        <div class="mb-2">
                            <label>What's your new candidate</label>
                            <input type="text" class="form-control" v-model="editParty.candidate">
                        </div>
                        <div class="mb-2">
                            <label>How about your new policy</label>
                            <input type="text" class="form-control" v-model="editParty.policy">
                        </div>       
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button @click="onToggleModalEdit('')" data-bs-toggle="modal" data-bs-target="#editModal" type="button" class="btn btn-secondary">Close</button>
                    <button @click="onUpdateParty" :disabled="sending" type="button" class="btn btn-success">
                        <span v-if="sending"> waiting...</span>
                        <span v-else>Update</span>
                    </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal edit -->

    </div>
    <!-- END #APP JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.1/axios.min.js" integrity="sha512-NbjaUHU8g0+Y8tMcRtIz0irSU3MjLlEdCvp82MqciVF4R2Ru/eaXHDjNSOvS6EfhRYbmQHuznp/ghbUvcC0NVw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.1/sweetalert2.all.min.js" integrity="sha512-KfbhdnXs2iEeelTjRJ+QWO9veR3rm6BocSoNoZ4bpPIZCsE1ysIRHwV80yazSHKmX99DM0nzjoCZjsjNDE628w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./js/app.js"></script>

</body>
</html>
/* =====================================================
   IDEAVAULT DASHBOARD JAVASCRIPT
===================================================== */


/* =====================================================
   USER NAME
===================================================== */

const userName =
    document.getElementById("userName");

const userAvatar =
    document.getElementById("userAvatar");


/*
   If register.js saved the student's name,
   display it automatically.
*/

const savedName =
    localStorage.getItem("ideaVaultUserName");


if (savedName) {

    userName.textContent = savedName;

    userAvatar.textContent =
        savedName.charAt(0).toUpperCase();

}


/* =====================================================
   PROFILE
===================================================== */

const profileForm =
    document.getElementById("profileForm");

const profileMessage =
    document.getElementById("profileMessage");


profileForm.addEventListener(
    "submit",
    function (event) {

        event.preventDefault();


        const name =
            document.getElementById("fullName")
            .value.trim();


        const course =
            document.getElementById("course")
            .value.trim();


        const year =
            document.getElementById("year")
            .value;


        const college =
            document.getElementById("college")
            .value.trim();


        const skills =
            document.getElementById("skills")
            .value.trim();


        if (name === "") {

            profileMessage.textContent =
                "Please enter your name.";

            return;

        }


        /* Save information */

        localStorage.setItem(
            "ideaVaultUserName",
            name
        );

        localStorage.setItem(
            "ideaVaultCourse",
            course
        );

        localStorage.setItem(
            "ideaVaultYear",
            year
        );

        localStorage.setItem(
            "ideaVaultCollege",
            college
        );

        localStorage.setItem(
            "ideaVaultSkills",
            skills
        );


        /* Update dashboard */

        userName.textContent = name;

        userAvatar.textContent =
            name.charAt(0).toUpperCase();


        profileMessage.textContent =
            "Profile saved successfully!";


        profileMessage.style.color =
            "#59caff";

    }
);


/* =====================================================
   SHARE PROJECT IDEA
===================================================== */

const ideaForm =
    document.getElementById("ideaForm");

const ideaMessage =
    document.getElementById("ideaMessage");

const myIdeas =
    document.getElementById("myIdeas");


ideaForm.addEventListener(
    "submit",
    function (event) {

        event.preventDefault();


        const title =
            document.getElementById("ideaTitle")
            .value.trim();


        const category =
            document.getElementById("ideaCategory")
            .value;


        const teamSize =
            document.getElementById("teamSize")
            .value;


        const description =
            document.getElementById("ideaDescription")
            .value.trim();


        const skills =
            document.getElementById("neededSkills")
            .value.trim();


        if (
            title === "" ||
            category === "" ||
            description === ""
        ) {

            ideaMessage.textContent =
                "Please fill in the required fields.";

            ideaMessage.style.color =
                "#78cfff";

            return;

        }


        /*
           Create the student's own idea card.
        */

        myIdeas.innerHTML = `

            <div class="idea-user-card">

                <span>
                    ${category}
                </span>

                <h4>
                    ${title}
                </h4>

                <p>
                    ${description}
                </p>

                <small>
                    Team members needed:
                    ${teamSize || "Not specified"}
                </small>

                <br>

                <small>
                    Skills:
                    ${skills || "Not specified"}
                </small>

            </div>

        `;


        ideaMessage.textContent =
            "Your project idea has been added!";


        ideaMessage.style.color =
            "#59caff";


        ideaForm.reset();

    }
);


/* =====================================================
   PROJECT CREATION
===================================================== */

const projectForm =
    document.getElementById("projectForm");

const projectWorkspace =
    document.getElementById("projectWorkspace");


projectForm.addEventListener(
    "submit",
    function (event) {

        event.preventDefault();


        const projectName =
            document.getElementById("projectName")
            .value.trim();


        const projectType =
            document.getElementById("projectType")
            .value;


        if (
            projectName === "" ||
            projectType === ""
        ) {

            alert(
                "Please enter the project details."
            );

            return;

        }


        document.getElementById(
            "displayProject"
        ).textContent =
            projectName;


        document.getElementById(
            "displayCategory"
        ).textContent =
            projectType;


        projectWorkspace.classList.remove(
            "hidden"
        );


        projectForm.reset();

    }
);


/* =====================================================
   TASK MANAGEMENT
===================================================== */

const addTask =
    document.getElementById("addTask");

const taskInput =
    document.getElementById("taskInput");

const taskList =
    document.getElementById("taskList");


addTask.addEventListener(
    "click",
    createTask
);


taskInput.addEventListener(
    "keydown",
    function (event) {

        if (event.key === "Enter") {

            createTask();

        }

    }
);


function createTask() {

    const taskName =
        taskInput.value.trim();


    if (taskName === "") {

        return;

    }


    const noTask =
        document.querySelector(".no-task");


    if (noTask) {

        noTask.remove();

    }


    const task =
        document.createElement("div");


    task.className =
        "task-item";


    task.innerHTML = `

        <div class="task-left">

            <input type="checkbox">

            <span>
                ${taskName}
            </span>

        </div>

        <button class="delete-task">
            Delete
        </button>

    `;


    taskList.appendChild(task);


    taskInput.value = "";


    /* Checkbox */

    const checkbox =
        task.querySelector("input");


    checkbox.addEventListener(
        "change",
        function () {

            if (this.checked) {

                task.classList.add(
                    "completed"
                );

            } else {

                task.classList.remove(
                    "completed"
                );

            }

            updateProgress();

        }
    );


    /* Delete */

    const deleteButton =
        task.querySelector(
            ".delete-task"
        );


    deleteButton.addEventListener(
        "click",
        function () {

            task.remove();

            updateProgress();


            if (
                taskList.children.length === 0
            ) {

                taskList.innerHTML = `

                    <p class="no-task">
                        No tasks added yet.
                    </p>

                `;

            }

        }
    );


    updateProgress();

}


/* =====================================================
   PROJECT PROGRESS
===================================================== */

function updateProgress() {

    const tasks =
        document.querySelectorAll(
            ".task-item"
        );


    const completed =
        document.querySelectorAll(
            ".task-item.completed"
        );


    if (tasks.length === 0) {

        document.getElementById(
            "progressBar"
        ).style.width = "0%";

        document.getElementById(
            "progressNumber"
        ).textContent = "0%";

        return;

    }


    const percentage =
        Math.round(
            (completed.length /
            tasks.length) * 100
        );


    document.getElementById(
        "progressBar"
    ).style.width =
        percentage + "%";


    document.getElementById(
        "progressNumber"
    ).textContent =
        percentage + "%";

}


/* =====================================================
   LOGOUT
===================================================== */

document.getElementById(
    "logoutBtn"
).addEventListener(
    "click",
    function () {

        localStorage.removeItem(
            "ideaVaultUserName"
        );

        window.location.href =
            "index.html";

    }
);
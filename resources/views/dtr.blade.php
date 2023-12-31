@extends('layouts.app')

@section('content')
<div class="flex flex-row h-screen bg-gray-200">
  <!-- Navigation Bar -->
  <div id="navbar" class="w-1/3 dark:bg-gray-800 text-white shadow-lg">
    <!-- Your content here -->
    @include('layouts.navbar')
  </div>
  <script>
    window.addEventListener('resize', function() {
      var element = document.getElementById('navbar');
      var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      
      if (screenWidth < 640) { // Adjust the breakpoint as needed
        element.classList.add('hidden');
      } else {
        element.classList.remove('hidden');
      }
    });
  </script>

  <!-- Main Content -->
  <div class="bg-gray-200 w-full h-screen">
    <div class="flex flex-col justify-center items-center  h-screen ">
      
      <div class="flex flex-row w-full pr-4 mt-15">
        <!-- Selector --> 
        <div class="container bg-white rounded-lg m-4 shadow-lg p-6 w-1/4">
          <h1 class="font-bold">Employee's DTR</h1>
          <hr class="my-3 h-0.5 border-t-0 bg-gray-400 opacity-100 dark:opacity-100"/>            
          
          {{-- Department DTR --}}
          <label for="department_select" class="sr-only">Select Department</label>
          <select id="department_select" class="block py-2.5 px-0 w-full text-sm text-black bg-transparent border-0 border-b-2 border-gray-200 appearance-none">
            <option value="" disabled selected class="text-gray-500">Select Department</option>
            @foreach ($departments as $department)
              <option value="{{ $department->id }}">{{ $department->name}}</option>
            @endforeach
          </select>
          
          <br>
          
          {{-- Select Year --}}
          <label for="year_select" class="sr-only">Select Year</label>
          <select id="year_select" class="block py-2.5 px-0 w-full text-sm text-black bg-transparent border-0 border-b-2 border-gray-200 appearance-none">
            {{-- <option selected class="text-gray-500">Year</option> --}}
            {{-- <option value="a">a1</option> --}}
            @for ($i = 0; $i < 6; $i++)
              @php
                $year = date('Y') - $i;
              @endphp
              <option value="{{ $year }}" {{ $year === date('Y') ? 'selected' : '' }}>{{ $year }}</option>
            @endfor
          </select>
          
          <br>
          
          {{-- Select Month --}}
          <label for="month_select" class="sr-only">Select Month</label>
          <select id="month_select" class="block py-2.5 px-0 w-full text-sm text-black bg-transparent border-0 border-b-2 border-gray-200 appearance-none">
            {{-- <option selected class="text-gray-500">Month</option> --}}
            {{-- <option value="a">a1</option> --}}
            @for ($month = 1; $month <= 12; $month++)
              <option value="{{ $month }}" {{ $month === date('n') ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
            @endfor
          </select>
        </div>
        <!-- Search Employee -->
        <div class="container bg-white rounded-lg m-4 shadow-lg flex flex-col w-full overflow-y-auto h-80">
        <div class="flex flex-row gap-15 p-5">
            <h1 class="font-bold mb-4 flex-grow">Employees</h1>
            <form id="searchForm" class="flex">
                <label for="keyword" class="sr-only">Search</label>
                <div class="relative flex">
                <input type="search" id="keyword" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50" placeholder="Search by first name or last name" required>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 ml-2">
                    <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </button>
                </div>
            </form>
            </div>

        <div id="results"></div>
        </div>
      </div>
      <div id="dtr" class="container bg-white rounded-lg m-10 shadow-lg w-2/3 p-2 ">
        <h1>The DTR will be displayed here when you select an employee.</h1>
      </div>
    </div>
  </div>
</div>

          <script>

            // Function to convert 24-hour format time to 12-hour format.
            function convertTo12HourFormat(timeString) {
              const date = new Date(timeString);
              const hours = date.getHours();
              const minutes = date.getMinutes();
              const ampm = hours >= 12 ? 'PM' : 'AM';
              const formattedHours = (hours % 12 === 0) ? 12 : hours % 12;
              return `${formattedHours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
            }

            // Function to extract the day from the time format
            function extractDayFromDate(timeString){
              const date = new Date(timeString);
              const day = date.getDate();
              return day;
            }
            // Function to extract the month in text format from the time format
            function extractMonthFromDate(timeString) {
              const date = new Date(timeString);
              const month = date.toLocaleString('default', { month: 'long' });
              return month;
            }


            // Get the selected year and month from the HTML select elements
            const selectedYear = document.getElementById('year_select').value;
            const selectedMonth = document.getElementById('month_select').value;

            const monthSelect = document.getElementById('month_select');
            // Add an event listener for the 'change' event
            monthSelect.addEventListener('change', function () {
              // Get the selected value (the selected month number)
              const selectedMonth = monthSelect.value;

              // Optionally, you can also get the selected month name from the option text
              const selectedMonthName = monthSelect.options[monthSelect.selectedIndex].text;

              // Log the selected month number and name to the console
              console.log('Selected Month Number:', selectedMonth);
              console.log('Selected Month Name:', selectedMonthName);
            });

            // Get the <select> element for the year
            const yearSelect = document.getElementById('year_select');

            // Add an event listener for the 'change' event
            yearSelect.addEventListener('change', function () {
              // Get the selected value (the selected year)
              const selectedYear = yearSelect.value;

              // Log the selected year to the console
              console.log('Selected Year:', selectedYear);
            });

            
            // Create a JavaScript Date object with the selected year and month
            const selectedDate = new Date(selectedYear, selectedMonth, 1);

            let department_id;
            document.getElementById('department_select').addEventListener('change', function() {
                department_id = this.value;
                console.log('Selected value:', department_id);
                console.log('Selected Year:', selectedYear);
                console.log('Selected Month:', selectedMonth);
            });

            document.getElementById('searchForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form submission
                department_id = document.getElementById('department_select').value;

                var keyword = document.getElementById('keyword').value;

                var resultsDiv = document.getElementById('results');
                var loadingDiv = document.createElement('div');
                loadingDiv.className = 'overflow-y-auto h-200';
                loadingDiv.innerHTML = '<div class="block w-full p-1 mb-2 pl-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">' +
                    '<h5 class="text-lg font-bold tracking-tight text-gray-900">Please wait...</h5>' +
                    '</div>';

                resultsDiv.innerHTML = '';
                resultsDiv.appendChild(loadingDiv);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('dtr.search') }}', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        resultsDiv.innerHTML = ''; // Clear previous loading message

                        if (xhr.status === 200) {
                            var employees = JSON.parse(xhr.responseText);

                            if (employees.length === 0) {
                                resultsDiv.innerHTML = '<p>No employees found.</p>';
                            } else {
                                employees.forEach(function(employee) {
                                    var resultItem = document.createElement('a');
                                    resultItem.className = 'block w-full p-1 mb-2 pl-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100';
                                    resultItem.href = '#';


                                    // Employee is clicked
                                    resultItem.addEventListener('click', function() {
                                      const selectedYear = document.getElementById('year_select').value;
                                      const selectedMonth = document.getElementById('month_select').value;

                                      console.log('Clicked employee:', employee.first_name + ' ' + employee.last_name);
                                      const employee_id = employee.id;

                                      // Prepare the request payload
                                      const requestData = {
                                        user_id: employee_id,
                                        selectedYear: selectedYear,
                                        selectedMonth: selectedMonth,
                                      };

                                      // Get the CSRF token value from the meta tag in your HTML layout
                                      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                                      // Include the CSRF token in the request headers
                                      const headers = {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                      };
                                      // Get the dtrDiv element
                                      const dtrDiv = document.getElementById('dtr');

                                      // Display the loading indicator before making the AJAX request
                                      dtrDiv.innerHTML = '<p>Loading...</p>';
                                      
                                      // Make an AJAX request to the controller method
                                      fetch('/getAttendances', {
                                        method: 'POST',
                                        headers: headers,
                                        body: JSON.stringify(requestData),
                                      })
                                      .then(response => {
                                        if (!response.ok) {
                                          throw new Error('Request failed with status: ' + response.status);
                                        }
                                        return response.json();
                                      })
                                      .then(data => {
                                        console.log('Attendance data:', data);

                                        // Check if the response contains at least one object
                                        if (data.length > 0) {
                                          // Create an empty string to store the table rows
                                          let tableRows = '';

                                          // Loop through each object in the data array
                                          

                                          var month;
                                          data.forEach(dtrData => {
                                            // Extract the relevant fields from the current data object
                                            const timeInAM = convertTo12HourFormat(dtrData.time_in_am);
                                            const timeOutAM = convertTo12HourFormat(dtrData.time_out_am);
                                            const timeInPM = convertTo12HourFormat(dtrData.time_in_pm);
                                            const timeOutPM = convertTo12HourFormat(dtrData.time_out_pm);
                                            
                                            const day = extractDayFromDate(dtrData.time_in_am);
                                            month = `<div> 
                                            <h1 class="text-xl font-bold p-4">DTR for the Month of ${extractMonthFromDate(dtrData.time_in_am)}</h1>
                                            <p>Employee: ${employee.last_name}, ${employee.first_name}</p>
                                            </div>`;
                                            // Add a table row with the extracted DTR data to the tableRows string
                                            tableRows += `
                                              <tr>
                                                <td class="border border-gray-300 px-4 py-2">${day}</td>
                                                <td class="border border-gray-300 px-4 py-2">${timeInAM}</td>
                                                <td class="border border-gray-300 px-4 py-2">${timeOutAM}</td>
                                                <td class="border border-gray-300 px-4 py-2">${timeInPM}</td>
                                                <td class="border border-gray-300 px-4 py-2">${timeOutPM}</td>
                                              </tr>
                                            `;
                                          });

                                          // Update the view with the complete table containing all the DTR data
                                          
                                          dtrDiv.innerHTML = `
                                          <div class="max-w-md mx-auto mt-4">
                                            ${month}

                                            <table class="w-full border-collapse border border-gray-300">
                                              <thead>
                                                <tr>
                                                  <th class="border border-gray-300 px-4 py-2"></th>
                                                  <th colspan="2" class="border border-gray-300 px-4 py-2">AM</th>
                                                  <th colspan="2" class="border border-gray-300 px-4 py-2">PM</th>
                                                </tr>
                                                <tr>
                                                  <th class="border border-gray-300 px-4 py-2">Day</th>
                                                 <th class="border border-gray-300 px-4 py-2"> Time In</th>
                                                 <th class="border border-gray-300 px-4 py-2"> Time Out </th>
                                                 <th class="border border-gray-300 px-4 py-2"> Time In </th>
                                                 <th class="border border-gray-300 px-4 py-2"> Time Out </th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                ${tableRows} <!-- Insert the generated table rows here -->
                                              </tbody>
                                            </table>
                                          </div>
                                          `;
                                        } else {
                                          // If no data is present in the response, display an error message or handle as needed.
                                          const dtrDiv = document.getElementById('dtr');
                                          dtrDiv.innerHTML = '<p>No attendance data found.</p>';
                                        }
                                      })
                                      .catch(error => {
                                        console.error('Error:', error);
                                      });
                                    });

                                    var h5 = document.createElement('h5');
                                    h5.className = 'text-lg font-bold tracking-tight text-gray-900';
                                    h5.textContent = employee.first_name + ' ' + employee.last_name;

                                    var p = document.createElement('p');
                                    p.className = 'font-light text-sm text-gray-500';
                                    p.textContent = employee.department;

                                    resultItem.appendChild(h5);
                                    resultItem.appendChild(p);
                                    resultsDiv.appendChild(resultItem);
                                });
                            }
                        } else {
                            console.error('Error: ' + xhr.status);
                        }
                    }
                };

                var requestData = JSON.stringify({
                    keyword: keyword,
                    department_id: department_id
                });

                xhr.send(requestData);
            });
        </script>


        </div>
    </div>
    
@endsection
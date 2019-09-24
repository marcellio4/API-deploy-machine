//once page has loaded, remove the preloader class (css transition fix)
$(window).bind("load", function() {
  $("body").removeClass("preload");
});

//cookie functions
function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires=" + d.toGMTString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function deleteCookie(name) {
  setCookie(name, '', -1);
}
//modal popups
(function($) {

  modal = function(opts) {
    opts = $.extend({
      // colour: '#0c8',
      class: 'modal',
      modalClass: 'modal-success',
      uniqueID: 'm-' + Math.floor(Math.random() * 1000) + 1,
      title: 'Message here',
      content: '',
      extra: '',
      autoClose: false,
      closeCallback: function() {},
      confirmCallback: function() {}
    }, opts);


    openModal = function() {
      var underlay = $('<div />', {
        class: opts.class + '-underlay',
        id: opts.uniqueID + '-underlay'
      });

      var overlay = $('<div />', {
        class: opts.class,
        id: opts.uniqueID
      }).append(
        $('<h2 />', {
          class: 'title'
        }).text(opts.title).append(
          $('<div />', {
            class: 'closer'
          })
        )
      );
      if (opts.content) {
        overlay.append(
          $('<div />', {
            class: 'content'
          }).append(opts.content).append(
            $('<div />', {
              class: 'extra'
            }).append(opts.extra)
          )
        );
      }


      $('body').append(underlay);
      $('body').append(overlay);
      $('body').addClass(opts.class + '-active');

      overlay.addClass(opts.modalClass);

      if (opts.confirm) {
        overlay.addClass('with-confirm');
        overlay.append(
          $('<div />', {
            class: 'confirm-wrapper'
          }).append(
            $('<a />', {
              href: opts.confirm.link,
              class: 'confirm'
            }).text(opts.confirm.text)
          )
        );
        overlay.find(' .confirm').on('click', function() {
          removeModal(opts.confirmCallback);
          return false;
        });
      }

      setTimeout(function() {
        $('#' + opts.uniqueID + '-underlay').css({
          'opacity': '1'
        });
        $('#' + opts.uniqueID).addClass('active');
        // close modal on outside click
        /*  $('.' + opts.class + '-underlay').on('click', function(){
             removeModal(opts.closeCallback);
         }); */
        $('.' + opts.class + ' .closer').on('click', function() {
          removeModal(opts.closeCallback);
        });
      }, 50);

      if (opts.autoClose) {
        overlay.addClass('modal-autoclose');
        setTimeout(function() {
          removeModal(opts.closeCallback);
        }, 1000);
      }
    }

    removeModal = function(callback) {
      //immediatly hide the underlay and hide the modal
      $('#' + opts.uniqueID + '-underlay').css({
        'opacity': '0'
      });
      $('#' + opts.uniqueID).removeClass('active');
      //remove elements after 350
      setTimeout(function() {
        $('#' + opts.uniqueID + '-underlay').remove();
        $('#' + opts.uniqueID).remove();
        $('body').removeClass(opts.class + '-active');
      }, 350);
      //call the callback function after 3000
      setTimeout(function() {
        callback();
      }, 1000);
    }

    this.openModal();

    return this;

  }
})(jQuery);
$(document).ready(function() {


  /***************** */
  //create form
  /***************** */
  $('#create-open-button').on('click', function() {
    $(this).siblings('form').css('display', 'inline-block');
    $(this).hide();
  });
  $('#create-close-button').on('click', function() {
    $(this).parent('form').hide();
    $(this).parent('form').siblings('#create-open-button').css('display', 'inline-block');
  });

  /***************** */
  //Progress bars
  /***************** */
  $(".meter > span").each(function() {
    let free = $(this).data('free');
    let total = $(this).data('total');
    let used = total - free;
    let percent = (used / total) * 100;
    var className = '';

    $(this).parents('.storage').find('.free').text(Math.floor(free));

    if (percent > 70) {
      className = 'alert';
    }
    $(this).addClass(className);
    $(this).animate({
      width: percent + '%'
    }, 1200);
  });

  /***************** */
  //Show user's preferred view
  /***************** */

  // display as default only mine
  if (getCookie('only') === 'mine') {
    $('.table-row:not(.' + $('.name span').text().toLowerCase() + ')').hide();
    $('.button-secondary').text('View all');
  }
  // display as default all
  if (getCookie('view') === 'all') {
    $('.table-row').show();
    $('.button-secondary').text('View mine');
  }

  $('#wrapper').show();
  /***************** */
  //modals examples
  /***************** */

  /* modal({
   	//error example
  	modalClass: 'modal-error',
   	title: 'There has been an error',
   	autoClose: true

   	//success example
  	modalClass: 'modal-success',
   	title: 'There has been a success',
  	autoClose: true

  	//default example

  	modalClass: 'modal-default',
  	title: 'Modal Title',
  	content: 'Content here',
   	autoClose: false
  }); */

  /***************** */
  // show loading screen
  /***************** */

  $('body').on('click', '.button-loading', function() {
    $('.loading-overlay').show();
    setTimeout(function() {
      $('.loading-overlay').hide();
    }, 850);
  });
  /***************** */
  /* copy to clipboard and show tooltip on click and then hide */
  /***************** */

  const clipboard = new ClipboardJS('.button-copy');
  clipboard.on('success', function(e) {
    console.log(e);
    e.clearSelection();
    setTooltip(e.trigger, 'Copied!');
  });

  clipboard.on('error', function(e) {
    console.log(e);
    setTooltip(e, 'Failed!');
  });

  function setTooltip(btn, message) {
    var id = '#' + btn.id;
    var tooltiptext = id + ' + .tooltiptext';

    origMessage = $(tooltiptext).html();
    $(tooltiptext).html(message);

    $(tooltiptext).css({
      'opacity': '1',
      'visibility': 'visible',
      'bottom': '160%'
    });
    hideTooltip(btn, origMessage);
  }

  function hideTooltip(btn, origMessage) {

    var id = '#' + btn.id;
    var tooltiptext = id + ' + .tooltiptext';

    setTimeout(function() {
      //$(tooltiptext).html(origMessage);
      $(tooltiptext).css({
        'bottom': '150%',
        'opacity': '0',
        'visibility': 'hidden'
      });
    }, 1000);
  }

  //add tooltip after all button copy buttons
  $('.button-copy').after('<span class="tooltiptext">Copied!</span>');

  /***************** */
  // theme CSS switcher
  /***************** */

  $('body').on('change', '#themeSwitch', function(event) {
    if ($(this).prop('checked')) {
      $('body').attr('data-theme', 'light');
      // set light cookie
      setCookie("theme", "light", 365);
    } else {
      $('body').removeAttr('data-theme');
      // delete cookie
      deleteCookie("theme");
    }
  });

  //initial check for cookie, if light set theme up for light
  if (getCookie("theme") === "light") {
    $('body').attr('data-theme', 'light');
    $('.switch__input').attr('checked', true);
  }


  // Extends menu for the user after click
  $('body').on('click', '.table-data', function() {
    $(this).parent('.table-row').toggleClass('active');
  });

  $('body').on('click', '.actions button', function(event) {
    event.stopPropagation();
  });

  $('body').on('click', '.actions .test', function(e) {
    e.preventDefault();
    alert('hello');
  });
  // Sorting name of properties on our home page
  var properties = [
    'owner',
    'id',
    'name',
    'date',
    'desc',
  ];

  $.each(properties, function(i, val) {

    var orderClass = '';

    $("#" + val).click(function(e) {
      e.preventDefault();
      $('.filter__link.filter__link--active').not(this).removeClass('filter__link--active');
      $(this).toggleClass('filter__link--active');
      $('.filter__link').removeClass('asc desc');

      if (orderClass == 'desc' || orderClass == '') {
        $(this).addClass('asc');
        orderClass = 'asc';
      } else {
        $(this).addClass('desc');
        orderClass = 'desc';
      }

      var parent = $(this).closest('.header__item');
      var index = $(".header__item").index(parent);
      var $table = $('.table-content');
      var rows = $table.find('.table-row').get();
      var isSelected = $(this).hasClass('filter__link--active');
      var isNumber = $(this).hasClass('filter__link--number');

      rows.sort(function(a, b) {

        var x = $(a).find('.table-data').eq(index).text().toLowerCase();
        var y = $(b).find('.table-data').eq(index).text().toLowerCase();

        if (isNumber == true) {

          if (isSelected) {
            return x - y;
          } else {
            return y - x;
          }

        } else {

          if (isSelected) {
            if (x < y) return -1;
            if (x > y) return 1;
            return 0;
          } else {
            if (x > y) return -1;
            if (x < y) return 1;
            return 0;
          }
        }
      });

      $.each(rows, function(index, row) {
        $table.append(row);
      });

      return false;
    });
  });

  // Error validation for login page if fields are empty
  $('#btn').click(function(e) {
    var span = $('.hide');
    var name = $('#username');
    var pwd = $('#password');
    if (name.val() == '' || pwd.val() == '') {
      span.show();
      e.preventDefault();
    }
  });

  // Creating new pool
  $('body').on('click', '#envpool', function(e) {
    e.preventDefault();
    var pool = $('#pool').val();
    var name = $('#newname').val();
    var msg = 'Pool and name can not be empty';
    var form = 'envvalue=' + pool + '&newname=' + name;
    if (pool == '' || name == '') {
      modal({
        modalClass: 'modal-error',
        title: 'Name and pool can not be empty',
        autoClose: false
      });
    } else {
      modal({
        modalClass: 'modal-default',
        title: 'Are you sure you want to create new pool?',
        autoClose: false,
        confirm: {
          link: '#',
          text: 'Confirm'
        },
        confirmCallback: function() {
          $.ajax({
            type: 'POST',
            data: form,
            url: 'index.php?page=tools',
            success: function(data) {
              if (data == 1) {
                modal({
                  modalClass: 'modal-success',
                  title: 'Your environment has been successfully deployed',
                  autoClose: true
                });
                setTimeout(function() { // wait for 2 secs(2)
                  location.reload(); // then reload the page.(3)
                }, 2000);
              } else {
                modal({
                  modalClass: 'modal-error',
                  title: 'Something went wrong',
                  autoClose: false
                });
              }
            },
            error: function(data) {
              modal({
                modalClass: 'modal-error',
                title: 'Could not run the script',
                autoClose: false
              });
              $(".loading-overlay").hide();
            }
          });
        }
      });
    }
  });

  // validation deploy machines environment names can not be empty
  $('body').on('click', '#uk', function(e) {
    $('.error').hide();
    if (submit($('#ukenvname'), '.uk')) {
      e.preventDefault();
    }
  });

  $('body').on('click', '#us', function(e) {
    $('.error').hide();
    if (submit($('#usenvname'), '.us')) {
      e.preventDefault();
    }
  });

  $('body').on('click', '#cto', function(e) {
    $('.error').hide();
    if (submit($('#ctoenvname'), '.cto')) {
      e.preventDefault();
    }
  });

  // Add description to page and update in database
  $("form[name=save]").submit(function(e) {
    e.preventDefault();
    var tableRow = $(this).parents('.table-row');
    var formData = $(this).serialize();
    var id = getId(tableRow.find('textarea'));
    var div = getId(tableRow.find('.desc'));
    var text = $('#' + id).val();
    if (text !== "") {
      $.ajax({
        type: 'POST',
        data: formData,
        url: 'index.php?page=home',
        success: function(data) {
          $('#' + div).text(text);
        },
        error: function(data) {
          modal({
            modalClass: 'modal-error',
            title: 'Did not update database',
            autoClose: false
          });
        }
      });
    } else {
      modal({
        modalClass: 'modal-error',
        title: 'Text field can not be blank',
        autoClose: false
      });
    }
  });

  addId('.desc', 'id-');
  addId('textarea', 'id_');

  // View jobs button
  $('.button-secondary').click(function() {
    var text = $('.button-secondary').text();
    var user = $('.name span').text();
    if (text === 'View mine') {
      $('.table-row:not(.' + user.toLowerCase() + ')').hide();
      $('.button-secondary').text('View all');
      setCookie('only', 'mine', 165);
      deleteCookie('view');
    }
    if (text === 'View all') {
      $('.table-row').show();
      $('.button-secondary').text('View mine');
      setCookie('view', 'all', 165);
      deleteCookie('only');
    }
  });

  // Change memory
  $('form[name=memory]').submit(function(e) {
    e.preventDefault();
    var name = $('select.memselect', this).attr('name');
    var value = $('select.memselect', this).val();
    var dataString = 'EnvName=' + name + '&sizeGB=' + value;
    if (value) {
      if ($.isNumeric(value)) {
        if (value > 10 || value < 1) {
          modal({
            modalClass: 'modal-error',
            title: 'Must be less than 10 and greater than 1',
            autoClose: false
          });
        } else {
          modal({
            modalClass: 'modal-default',
            title: 'Are you sure ?',
            autoClose: false,
            confirm: {
              link: '#',
              text: 'Confirm'
            },
            confirmCallback: function() {
              $.ajax({
                type: "POST",
                url: "index.php?page=home",
                data: dataString,
                cache: false,
                success: function(data) {
                  if (data == 1) {
                    modal({
                      modalClass: 'modal-success',
                      title: 'The memory has been changed',
                      autoClose: true
                    });
                  } else {
                    modal({
                      modalClass: 'modal-error',
                      title: 'Fail to change the memory' + data,
                      autoClose: false
                    });
                  }
                },
                error: function(data) {
                  modal({
                    modalClass: 'modal-error',
                    title: 'There has been an error ' + data,
                    autoClose: false
                  });
                }
              });
            }
          });
        }
      } else {
        modal({
          modalClass: 'modal-error',
          title: 'It must be a number',
          autoClose: false
        });
      }
    } else {
      modal({
        modalClass: 'modal-error',
        title: 'Please Fill the memory field',
        autoClose: false
      });
    }
  });

  // deploy set
  $('form[name=deploy]').on('submit', function(e) {
    e.preventDefault();
    var getNames = $(this).parents('.table-row').find('.env_name').text();
    var split = getNames.split(' ');
    var value = $(this).parents('.table-row').find('.Env_deploy').val();
    var value2 = $(this).parents('.table-row').find('.Env_deployBR').val();
    if (value) {
      if (split[0] && split[1]) {
        var send = 'machine-1=' + split[0] + '&machine-2=' + split[1] + '&selected=' + value + '&Env_deployBR=' + value2;
      } else {
        var send = 'machine-1=' + split[0] + '&selected=' + value + '&Env_deployBR=' + value2;
      }
      var $div = $('<div>', {
        class: "loading"
      });
      $(this).parents('.details-deploy').find('.btnSubmit').after($div);
      // console.log(split[0] + ' ' + split[1] + ' value2:' + value2);
      modal({
        modalClass: 'modal-default',
        title: 'Are you sure to deploy?',
        autoClose: false,
        confirm: {
          link: '#',
          text: 'Confirm'
        },
        confirmCallback: function() {
          $.ajax({
            data: send,
            type: 'post',
            url: 'index.php?page=home',
            success: function(data) {
              $('.details-deploy').find('.loading').remove();
              if (data == 1) {
                modal({
                  modalClass: 'modal-success',
                  title: 'Your job has started to pull from github',
                  autoClose: true
                });
              } else {
                modal({
                  modalClass: 'modal-error',
                  title: 'Could not pull the job',
                  autoClose: false
                });
              }
            },
            error: function(data) {
              modal({
                modalClass: 'modal-error',
                title: 'script error ' + data,
                autoClose: false
              });
            }
          });
        }
      });
    } else {
      modal({
        modalClass: 'modal-error',
        title: 'Please select Job name',
        autoClose: false
      });
    }

  });

  $('.start-stop').click(function(e) {
    e.preventDefault();
    var btn = $(this);
    var start_stop = btn.parents('.actions').find('.start-stop').text();
    var ids = btn.closest('.table-row').attr('id');
    var getnames = $(this).parents('.table-row').find('.env_name').text();
    var spliting = getnames.split(' ');
    if (spliting[0] && spliting[1]) {
      var sending = 'comp-1=' + spliting[0] + '&comp-2=' + spliting[1] + '&NAME=' + start_stop + '&ids=' + ids;
    } else {
      var sending = 'comp-1=' + spliting[0] + '&NAME=' + start_stop + '&ids=' + ids;
    }
    $.ajax({
      data: sending,
      type: 'post',
      url: 'index.php?page=home',
      beforeSend: function() {
        $(".loading-overlay").show();
      },
      success: function(data) {
        $(".loading-overlay").hide();
        if (data === 'Start') {
          btn.text('Stop');
          $('#' + ids).removeClass('dead');
          modal({
            modalClass: 'modal-success',
            title: 'Your machine has started',
            autoClose: true
          });
        } else if (data === 'Stop') {
          btn.text('Start');
          btn.parents('.table-row').addClass('dead');
          modal({
            modalClass: 'modal-success',
            title: 'Your machine has stopped',
            autoClose: true
          });
        } else {
          modal({
            modalClass: 'modal-error',
            title: 'Problem occur on Jenkins',
            autoClose: false
          });
        }
      },
      error: function(data) {
        modal({
          modalClass: 'modal-error',
          title: 'Something went wrong with script',
          autoClose: false
        });
      }
    });
  });

  $('.check').click(function(e) {
    e.preventDefault();
    var btn = $(this);
    var check = btn.data('action');
    var ids = btn.closest('.table-row').attr('id');
    var sending = 'NAME=' + check + '&ids=' + ids;
    $.ajax({
      data: sending,
      type: 'post',
      url: 'index.php?page=home',
      beforeSend: function() {
        $(".loading-overlay").show();
      },
      success: function(data) {
        $(".loading-overlay").hide();
        if (data === 'Fail') {
          modal({
            modalClass: 'modal-error',
            title: data,
            autoClose: false
          });
        }
        modal({
          modalClass: 'modal-success',
          title: data,
          autoClose: true
        });
      },
      error: function(data) {
        modal({
          modalClass: 'modal-error',
          title: data,
          autoClose: false
        });
      }
    });
  });

  $('.revert').click(function(e) {
    e.preventDefault();
    var btn = $(this);
    var check = btn.data('action');
    var ids = btn.closest('.table-row').attr('id');
    var sending = 'NAME=' + check + '&ids=' + ids;
    $.ajax({
      data: sending,
      type: 'post',
      url: 'index.php?page=home',
      success: function(data) {
        if (data === 'Fail') {
          modal({
            modalClass: 'modal-error',
            title: data,
            autoClose: false
          });
        }
        modal({
          modalClass: 'modal-success',
          title: data,
          autoClose: true
        });
      },
      error: function(data) {
        modal({
          modalClass: 'modal-error',
          title: data,
          autoClose: false
        });
      }
    });
  });

  $('.delete').click(function() {
    var btn = $(this);
    var check = btn.data('action');
    var ids = btn.closest('.table-row').attr('id');
    var sending = 'NAME=' + check + '&ids=' + ids;
    modal({
      modalClass: 'modal-default',
      title: 'Are you sure you want to delete?',
      autoClose: false,
      confirm: {
        link: '#',
        text: 'Confirm'
      },
      confirmCallback: function() {
        $.ajax({
          data: sending,
          type: 'post',
          url: 'index.php?page=home',
          success: function(data) {
            switch (data) {
              case 'Success':
                setTimeout(function() { // wait for 5 secs(2)
                  location.reload(); // then reload the page.(3)
                }, 2000);
                break;
              case 'database':
                modal({
                  modalClass: 'modal-error',
                  title: 'Could not set in database to delete',
                  autoClose: false
                });
                break;
              default:
                modal({
                  modalClass: 'modal-error',
                  title: 'Failed to delete on Jenkins',
                  autoClose: false
                });
                break;
            }
          },
          error: function(data) {
            modal({
              modalClass: 'modal-error',
              title: data,
              autoClose: false
            });
          }
        });
      }
    });
  });

  $('.btn-view-log').on('click', function() {
    var $this = $(this);
    var getmachine = $this.parents('.table-row').find('.env_name').text();
    var spliting = getmachine.split(' ');
    if (spliting[0] && spliting[1]) {
      var out = 'comp1=' + spliting[0] + '&comp2=' + spliting[1];
    } else {
      var out = 'comp1=' + spliting[0];
    }
    $.ajax({
      data: out,
      type: 'post',
      url: 'index.php?page=home',
      success: function(data) {
        modal({
          modalClass: 'modal-default',
          title: 'View Log',
          autoClose: false,
          content: data
        });
      },
      error: function(data) {
        modal({
          modalClass: 'modal-error',
          title: 'Something went wrong ( script )',
          autoClose: false
        });
      }
    });
  });

  // pie chart
  $('form[name=chart]').submit(function(e) {
    e.preventDefault();
    var date = $('#start').val();
    var splitDate = date.split('-');
    var month = splitDate[1];
    var year = splitDate[0];
    var send = "month=" + month + "&year=" + year;
    $.ajax({
      data: send,
      type: 'post',
      url: 'index.php?page=chart',
      dataType: 'json',
      success: function(data) {
        if (data.length == 0) {
          var $p = $('<p>', {
            id: "nothing"
          });
          $('#chartdiv').append($p);
          $('#nothing').text('No Data Found');
        } else {
          pieChart(data);
        }
      },
      error: function(data) {
        modal({
          modalClass: 'modal-error',
          title: 'Something went wrong ( script )',
          autoClose: false
        });
      }
    });
  });

  //deploy host file on home page
  $('.depHost').on('click', function(e) {
    e.preventDefault();
    var $this = $(this);
    var findid = $this.closest('.table-row').attr('id');
    var data = 'deployid=' + findid;
    $.ajax({
      data: data,
      type: 'post',
      url: 'index.php?page=home',
      success: function(data) {},
      error: function(data) {
        modal({
          modalClass: 'modal-error',
          title: 'Something went wrong ( script )',
          autoClose: false
        });
      }
    });
  });

  // remove delete button to all users except my own environment if the user is not admin(willemv)
  $(function() {
    var name = $('.name span').text().toLowerCase();
    if (name !== 'willemv') {
      $('.table-row:not(.' + name + ')').find('.actions').remove();
    }
  });

  // Search by ip address
  $(function() {
    var $input = $("input[name='filtertext']"),
      $context = $('.highlightable .table-row'),
      instance = new Mark($context);
    $input.on("input", function() {
      var term = $(this).val();
      instance.show().unmark();
      if (term) {
        var options = {
          "separateWordSearch": false,
          "exclude": ["option", ".visuallyhidden", "button", ".tooltiptext"],
          "done": function() {
            $context.not(":has(mark)").hide();
          }
        }
        instance.mark(term, options)
      }
    });
  });

  // update date for input tag in pie chart
  $(function() {
    var date = new Date(),
      year = date.getFullYear(),
      month = ("0" + (date.getMonth() + 1)).slice(-2),
      format = year + '-' + month;
    $('#start').val(format);
  });


}); // document .ready finish line


function pieChart(dat) {
  // Set theme
  am4core.useTheme(am4themes_dark);
  am4core.useTheme(am4themes_animated);
  // Create chart instance
  var chart = am4core.create("chartdiv", am4charts.PieChart3D);

  // Add data
  chart.data = dat;


  // Add and configure Series
  var pieSeries = chart.series.push(new am4charts.PieSeries3D());
  pieSeries.dataFields.value = "deployed";
  pieSeries.dataFields.category = "username";
}

function getId(ele) {
  var el = $(ele).attr('id');
  return el;
}

function addId(target, text) {
  $(function() {
    $(target).attr('id', function(i) {
      return text + (i + 1);
    });
  });
}

// check if on submit is input empty
function submit(field1, tag) {
  var input1 = field1.val();
  var msg = 'Environment name can not be Empty';
  if (input1 == '') {
    message(msg, tag);
    return true;
  }
  return false;
}

function message(txt, tag) {
  var m = $(tag);
  // set text before displaying message
  m.children("span").text(txt);
  m.children('span').addClass('off').show();
  // display message
  m.fadeIn("slow");
}
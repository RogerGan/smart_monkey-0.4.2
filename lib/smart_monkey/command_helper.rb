# coding: utf-8

module UIAutoMonkey
  module CommandHelper
    require 'Open3'

    def instruments_deviceinfo(device)
      `"instruments" -s devices | grep "#{device}"`.strip
    end

    def is_simulator
      deviceinfo = instruments_deviceinfo(device)
      if deviceinfo.include? "-"
        true
      else
        puts "is RealDevice"
        false
      end
    end

    def shell(cmds)
      puts "Shell: #{cmds.inspect}"
      Open3.popen3(*cmds) do |stdin, stdout, stderr|
        stdin.close
        return stdout.read
      end
    end

    def relaunch_app(device,app)
      if is_simulator
        puts "Start by xcrun xcrun simctl launch #{device}   #{app} >/dev/null 2>&1 & "
        `xcrun simctl launch #{device}   #{app} >/dev/null 2>&1 &`
      else
        puts " idevicedebug -u #{device} run #{app} >/dev/null 2>&1 & "
        `idevicedebug -u #{device} run #{app} >/dev/null 2>&1 &`
      end
    end

    def run_process(cmds)
      puts "Run: #{cmds.inspect}"
      device = cmds[2]
      app = cmds[-7]
      count = 0
      Open3.popen3(*cmds) do |stdin, stdout, stderr, thread|
        @tmpline = ""
        stdin.close
        app_hang_monitor_thread = Thread.start{
          sleep 30
          while true
            current_line = @tmpline
            sleep 30
            after_sleep_line = @tmpline
            if current_line == after_sleep_line
              puts "WARN: no response in log, trigger re-launch action."
              relaunch_app(device, app)
              count = count + 1
              puts "have relaunch_app #{count}"
              if count > 3
                puts "trigger re-launch failed, kill thread"
                app_hang_monitor_thread.kill
                instruments_stderr_thread.kill
                count = 0
              end
            end
          end
        }
        instruments_stderr_thread = Thread.start{
          stderr.each do |line|
            puts line
          end
        }
        stdout.each do |line|
          @tmpline = line.strip
          puts @tmpline
          if @tmpline =~ /MonkeyTest finish/ || @tmpline =~ /Script was stopped by the user/
            app_hang_monitor_thread.kill
          end
        end
        app_hang_monitor_thread.kill
        instruments_stderr_thread.kill
      end
    end

    def kill_all(process_name, signal=nil)
      signal = signal ? "-#{signal}" : ''
      # puts "killall #{signal} #{process_name}"
      Kernel.system("killall #{signal} '#{process_name}' >/dev/null 2>&1")
    end

    def xcode_path
      @xcode_path ||= shell(%w(xcode-select -print-path)).strip
    end

  end
end
